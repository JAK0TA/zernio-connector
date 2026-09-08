<?php

declare(strict_types=1);

namespace JAKOTA\ZernioConnector\Task;

use GuzzleHttp\Client;
use JAKOTA\ZernioConnector\Domain\Model\SocialMedia;
use JAKOTA\ZernioConnector\Domain\Repository\SocialMediaRepository;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use Zernio\Api\AnalyticsApi;
use Zernio\Api\PostsApi;
use Zernio\Configuration;
use Zernio\Model\GetAnalytics200Response;
use Zernio\Model\Post;
use Zernio\Model\PostsListResponse;

class ImportSocialMediaTask extends AbstractTask {
  /**
   * @var array<int, string>
   */
  private $allowedExtensions = [
    'jpg',
    'jpeg',
    'png',
    'gif',
    'webp',
    'avif',
  ];

  private ?SocialMedia $post = null;

  /**
   * @var array<int, string>
   */
  private $requiredConfiguration = [
    'platforms',
    'profileId',
    'limit',
    'storagePId',
    'accessToken',
  ];

  private ResourceFactory $resourceFactory;

  public function execute(): bool {
    $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

    $persistenceManager = GeneralUtility::makeInstance(PersistenceManagerInterface::class);
    $socialMediaRepository = GeneralUtility::makeInstance(SocialMediaRepository::class);

    // Extension configuration
    $extConf = (array) GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('zernio-connector');

    // check if all necessary configurations are set
    $this->checkExtConf($extConf);

    $platforms = explode(',', strval($extConf['platforms']));
    $profileId = strval($extConf['profileId']);
    $limit = intval($extConf['limit']);
    $storagePid = intval($extConf['storagePId']);
    if ($storagePid < 0) {
      throw new \RuntimeException(
        sprintf(
          'Die erforderliche Extension-Konfiguration "storagePid" ist kleiner als 0: %s',
          $storagePid
        ),
        1756974001
      );
    }
    $accessToken = strval($extConf['accessToken']);

    // Zernio API
    $zernioConfig = Configuration::getDefaultConfiguration()->setAccessToken($accessToken);
    $zernioPostApi = new PostsApi(new Client(), $zernioConfig);
    $zernioAnalyticsApi = new AnalyticsApi(new Client(), $zernioConfig);

    foreach ($platforms as $platform) {
      $platform = trim($platform);

      if ('' === $platform) {
        continue;
      }

      $zernioPosts = $zernioPostApi->listPosts(limit: $limit, source: 'external', platform: $platform, profile_id: $profileId, sort_by: 'created-desc', include_hidden: false);

      if (!$zernioPosts instanceof PostsListResponse) {
        continue;
      }

      foreach ($zernioPosts->getPosts() ?? [] as $zernioPost) {
        $platforms = $zernioPost->getPlatforms();

        if (empty($platforms)) {
          continue;
        }

        $platformData = $platforms[0];

        $postId = strval($platformData->getPlatformPostId());

        if ('' === $postId) {
          continue;
        }

        // Get analytics
        $zernioAnalytics = $zernioAnalyticsApi->getAnalytics(post_id: $postId, limit: $limit, source: 'external', platform: $platform, profile_id: $profileId);

        if (!$zernioAnalytics instanceof GetAnalytics200Response) {
          continue;
        }

        // Get post data
        $content = strval($zernioPost->getContent());
        $type = strval($platformData->getPlatform());
        $date = $zernioPost->getScheduledFor();
        $link = strval($platformData->getPlatformPostUrl());

        $likes = intval($zernioAnalytics->getAnalytics()?->getLikes() ?? 0);
        $comments = intval($zernioAnalytics->getAnalytics()?->getComments() ?? 0);

        // Get media URL
        $mediaUrl = $this->getMediaUrl($zernioPost);

        $mediaHash = null !== $mediaUrl ? md5($mediaUrl) : '';

        // Find existing post
        $this->post = $socialMediaRepository->findBy(['post_id' => $postId])->getFirst();

        // check for Existing post
        if ($this->post instanceof SocialMedia) {
          // If the post has not changed since the last update, skip it.
          if ($this->isUpToDate($zernioAnalytics)) {
            continue;
          }

          $this->post->setContent($content);
          $this->post->setType($type);
          $this->post->setPid($storagePid);
          $this->post->setLink($link);
          $this->post->setPublishDate($date);
          $this->post->setLikes($likes);
          $this->post->setComments($comments);

          // Update media only if the URL/hash changed.
          if ($mediaHash !== $this->post->getMediaHash()) {
            $this->updateMedia($mediaUrl, $mediaHash);
          }

          // @phpstan-ignore-next-line
          $socialMediaRepository->update($this->post);

          continue;
        }

        // Create new post
        $this->post = new SocialMedia();

        $this->post->setContent($content);
        $this->post->setPid($storagePid);
        $this->post->setType($type);
        $this->post->setLink($link);
        $this->post->setPublishDate($date);
        $this->post->setPostId($postId);
        $this->post->setLikes($likes);
        $this->post->setComments($comments);

        // Now create the FAL reference.
        if (null !== $mediaUrl) {
          $this->updateMedia($mediaUrl, $mediaHash);
        }

        // @phpstan-ignore-next-line
        $socialMediaRepository->add($this->post);
      }
    }

    $persistenceManager->persistAll();

    return true;
  }

  /**
   * @param array<mixed, mixed> $extConf
   */
  private function checkExtConf(array $extConf): void {
    foreach ($this->requiredConfiguration as $configurationKey) {
      if (!isset($extConf[$configurationKey]) || '' === trim(strval($extConf[$configurationKey]))) {
        throw new \RuntimeException(
          sprintf(
            'Die erforderliche Extension-Konfiguration "%s" ist nicht gesetzt.',
            $configurationKey
          ),
          1756974001
        );
      }
    }
  }

  private function createFileReference(FileInterface $file): ?FileReference {
    if (!$file instanceof File) {
      return null;
    }

    $fileReference = GeneralUtility::makeInstance(FileReference::class);
    $fileReference->setOriginalResource(
      $this->resourceFactory->createFileReferenceObject([
        'uid_local' => $file->getUid(),
        'uid_foreign' => StringUtility::getUniqueId('NEW'),
        'uid' => StringUtility::getUniqueId('NEW'),
      ])
    );

    return $fileReference;
  }

  private function getMediaUrl(Post $zernioPost): ?string {
    $mediaItems = $zernioPost->getMediaItems();

    if (empty($mediaItems)) {
      return null;
    }

    $url = $mediaItems[0]->getThumbnail();

    if (!is_string($url) || '' === $url) {
      return null;
    }

    return $url;
  }

  // Download an image and store it in FAL.
  private function importMedia(string $url): ?FileInterface {
    $storage = $this->resourceFactory->getDefaultStorage();

    if (null === $storage) {
      return null;
    }

    $folderIdentifier = 'socialmedia';

    // Create folder if necessary.
    if (!$storage->hasFolder($folderIdentifier)) {
      $folder = $storage->createFolder($folderIdentifier);
    } else {
      $folder = $storage->getFolder($folderIdentifier);
    }

    // Determine file extension.
    $extension = strtolower(
      pathinfo(
        strval(parse_url($url, PHP_URL_PATH)),
        PATHINFO_EXTENSION
      )
    );

    if (!in_array($extension, $this->allowedExtensions, true)) {
      $extension = 'jpg';
    }

    /*
     * Stable filename.
     *
     * Same URL = same file.
     */
    $filename = md5($url).'.'.$extension;

    // Reuse existing file.
    if ($folder->hasFile($filename)) {
      return $folder->getFile($filename);
    }

    // Download using TYPO3 HTTP client.
    $requestFactory = GeneralUtility::makeInstance(
      RequestFactory::class
    );

    try {
      $response = $requestFactory->request(
        $url,
        'GET',
        [
          'timeout' => 30,
          'allow_redirects' => true,
        ]
      );
    } catch (\Throwable) {
      return null;
    }

    if (200 !== $response->getStatusCode()) {
      return null;
    }

    // Check Content-Type.
    $contentType = strtolower(
      $response->getHeaderLine('Content-Type')
    );

    if (
      '' !== $contentType
      && !str_starts_with($contentType, 'image/')
    ) {
      return null;
    }

    $content = $response
      ->getBody()
      ->getContents()
    ;

    if ('' === $content) {
      return null;
    }

    // Create FAL file.
    try {
      $file = $folder->createFile($filename);
      $file->setContents($content);
    } catch (\Throwable) {
      return null;
    }

    return $file;
  }

  private function isUpToDate(GetAnalytics200Response $analytics): bool {
    $lastUpdated = $analytics->getAnalytics()?->getLastUpdated();

    if (!$lastUpdated instanceof \DateTimeInterface) {
      return false;
    }

    $crdate = $this->post?->getCrdate();

    if (null === $crdate) {
      return false;
    }

    return $crdate >= $lastUpdated;
  }

  // Delete the existing sys_file_reference.
  private function removeFileReference(): void {
    $this->post?->getMedia()?->getOriginalResource()->delete();
    $this->post?->setMedia(null);
    $this->post?->setMediaHash('');
  }

  // Download the media and create the FAL reference
  private function updateMedia(?string $mediaUrl, string $mediaHash): void {
    if (null === $mediaUrl) {
      if (null !== $this->post?->getMedia()) {
        // Delete the existing sys_file_reference.
        $this->removeFileReference();
      }

      return;
    }

    // Download/reuse FAL file
    $file = $this->importMedia($mediaUrl);

    if (null === $file) {
      return;
    }

    // Remove previous reference
    if (null !== $this->post?->getMedia()) {
      $this->removeFileReference();
    }

    // Create new sys_file_reference
    $fileReference = $this->createFileReference($file);
    if (null !== $fileReference) {
      $this->post?->setMedia($fileReference);
      $this->post?->setMediaHash($mediaHash);
    }
  }
}

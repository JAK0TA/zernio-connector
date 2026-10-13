<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\ZernioConnector\Controller;

use JAKOTA\ZernioConnector\Domain\Repository\SocialMediaRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class SocialMediaController extends ActionController {
  public function __construct(protected SocialMediaRepository $socialMediaRepository, protected ExtensionConfiguration $extensionConfiguration) {}

  public function listAction(): ResponseInterface {
    $platform = $this->settings['platform'];
    $limit = intval($this->settings['limit']);
    $respectSysLanguage = boolval($this->settings['respectSysLanguage']);

    $platforms = array_map('trim', explode(',', $platform));
    $postsByPlatform = [];

    foreach ($platforms as $platform) {
      $postsByPlatform[$platform] = $this->socialMediaRepository->findByTypeAndMedia(
        $platform,
        $limit,
        $respectSysLanguage,
      );
    }

    $this->view->assign('platforms', $postsByPlatform);

    return $this->htmlResponse();
  }
}

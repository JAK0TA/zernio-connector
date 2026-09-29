<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\ZernioConnector\Controller;

use JAKOTA\ZernioConnector\Domain\Repository\SocialMediaRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class SocialMediaController extends ActionController {
  public function __construct(protected SocialMediaRepository $socialMediaRepository) {}

  public function listAction(): ResponseInterface {
    $platform = $this->settings['platform'];
    $template = $this->settings['template'];

    $extConf = (array) GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('zernio-connector');
    $limit = intval($extConf['limit']);

    $platforms = array_map('trim', explode(',', $platform));
    $postsByPlatform = [];
    foreach ($platforms as $platform) {
      if ('start' == $template) {
        $postsByPlatform[$platform] = $this->socialMediaRepository->findByTypeAndMedia($platform, 2);
      } else {
        $postsByPlatform[$platform] = $this->socialMediaRepository->findByTypeAndMedia($platform, $limit);
      }
    }

    $this->view->assign('platforms', $postsByPlatform);

    return $this->htmlResponse();
  }
}

<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\ZernioConnector\Controller;

use JAKOTA\ZernioConnector\Domain\Repository\SocialMediaRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class SocialMediaController extends ActionController {
  public function __construct(protected SocialMediaRepository $socialMediaRepository) {}

  public function listAction(): ResponseInterface {
    $platform = $this->settings['platform'] ?? '';

    $platforms = array_map('trim', explode(',', $platform));
    $postsByPlatform = [];

    foreach ($platforms as $platform) {
      $postsByPlatform[$platform] = $this->socialMediaRepository->findBy([
        'type' => $platform,
      ]);
    }

    $this->view->assign('posts', $postsByPlatform);

    return $this->htmlResponse();
  }
}

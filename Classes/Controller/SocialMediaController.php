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
    $this->view->assign('posts', $this->socialMediaRepository->findAll());

    return $this->htmlResponse();
  }
}

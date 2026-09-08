<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

use JAKOTA\ZernioConnector\Controller\SocialMediaController;
use JAKOTA\ZernioConnector\Task\ImportSocialMediaTask;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3')) {
  exit;
}
(function () {
  ExtensionUtility::configurePlugin(
    'zernio-connector',
    'socialmedia_list',
    [
      SocialMediaController::class => 'list',
    ],
    // non-cacheable actions
    [
      SocialMediaController::class => 'list',
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
  );

  // @phpstan-ignore-next-line
  $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][ImportSocialMediaTask::class] = [
    'extension' => 'zernio-connector',
    'title' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.task.title',
    'description' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.task.description',
  ];
})();

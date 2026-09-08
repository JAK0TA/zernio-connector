<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3')) {
  exit;
}

// SocialMedia List Plugin
$pluginCType = ExtensionUtility::registerPlugin(
  'zernio-connector',
  'socialmedia_list',
  'Social Media Wall',
  '',
  'SocialMedia'
);

ExtensionManagementUtility::addToAllTCAtypes('tt_content', '--div--;Configuration,pi_flexform,', $pluginCType, 'after:subheader');
ExtensionManagementUtility::addPiFlexFormValue(
  '*',
  'FILE:EXT:zernio-connector/Configuration/FlexForm/SocialMedia.xml',
  $pluginCType
);

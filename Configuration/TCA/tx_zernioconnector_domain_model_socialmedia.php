<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

return [
  'ctrl' => [
    'title' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.post',
    'label' => 'type',
    'label_alt' => 'content',
    'label_alt_force' => true,
    'tstamp' => 'tstamp',
    'crdate' => 'crdate',
    'delete' => 'deleted',
    'default_sortby' => 'publish_date DESC',
    'rootLevel' => -1,
    'versioningWS' => true,
    'iconfile' => 'EXT:rp_events/Resources/Public/Icons/tx_rpevents_domain_model_event.svg',
    'languageField' => 'sys_language_uid',
    'transOrigPointerField' => 'l10n_parent',
    'transOrigDiffSourceField' => 'l10n_diffsource',
    'translationSource' => 'l10n_source',
    'searchFields' => 'content',
    'enablecolumns' => [
      'disabled' => 'hidden',
      'starttime' => 'starttime',
      'endtime' => 'endtime',
    ],
    'security' => [
      'ignorePageTypeRestriction' => true,
    ],
  ],
  'columns' => [
    'content' => [
      'label' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.content',
      'config' => [
        'type' => 'text',
        'cols' => 40,
        'rows' => 15,
        'eval' => 'trim',
        'required' => true,
      ],
    ],
    'post_id' => [
      'label' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.post_id',
      'config' => [
        'type' => 'input',
        'cols' => 40,
        'rows' => 15,
        'eval' => 'trim',
        'required' => true,
      ],
    ],
    'type' => [
      'label' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.type',
      'config' => [
        'type' => 'input',
        'size' => 50,
        'eval' => 'trim',
        'required' => true,
      ],
    ],
    'link' => [
      'label' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.link',
      'config' => [
        'type' => 'input',
        'size' => 50,
        'eval' => 'trim',
        'required' => true,
      ],
    ],
    'publish_date' => [
      'label' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.publish_date',
      'config' => [
        'type' => 'input',
        'renderType' => 'inputDateTime',
        'size' => 20,
        'eval' => 'datetime',
        'default' => null,
      ],
    ],
    'crdate' => [
      'label' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.crdate',
      'config' => [
        'type' => 'input',
        'renderType' => 'inputDateTime',
        'size' => 12,
        'eval' => 'datetime',
        'default' => null,
      ],
    ],
    'media' => [
      'label' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.media',
      'config' => [
        'type' => 'file',
        'allowed' => 'common-image-types',
        'minitems' => 0,
      ],
    ],
    'likes' => [
      'label' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.likes',
      'config' => [
        'type' => 'input',
        'size' => 20,
        'eval' => 'trim',
      ],
    ],
    'comments' => [
      'label' => 'LLL:EXT:zernio-connector/Resources/Private/Language/locallang.xlf:socialmedia.comments',
      'config' => [
        'type' => 'input',
        'size' => 20,
        'eval' => 'trim',
      ],
    ],
    'media_hash' => [
      'config' => [
        'type' => 'input',
        'readOnly' => true,
      ],
    ],
  ],
  'palettes' => [
    'timeRestriction' => ['showitem' => 'starttime, endtime'],
    'language' => ['showitem' => 'sys_language_uid, l10n_parent'],
    'feedback' => ['showitem' => 'likes, comments'],
    'platform' => ['showitem' => 'type, link'],
  ],
  'types' => [
    '0' => [
      'showitem' => 'content,--palette--;;platform, publish_date, --palette--;;feedback,  media,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden,--palette--;;timeRestriction,
         ',
    ],
  ],
];

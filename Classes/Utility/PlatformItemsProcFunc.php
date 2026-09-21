<?php

declare(strict_types=1);

namespace JAKOTA\ZernioConnector\Utility;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class PlatformItemsProcFunc {
  public function getItems(array &$config): void {
    $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);

    $platforms = $extensionConfiguration->get(
      'zernio-connector',
      'platforms'
    );

    if (!is_string($platforms) || '' === $platforms) {
      return;
    }

    foreach (explode(',', $platforms) as $platform) {
      $platform = trim($platform);

      if ('' === $platform) {
        continue;
      }

      $config['items'][] = [
        ucfirst($platform),
        $platform,
      ];
    }
  }
}

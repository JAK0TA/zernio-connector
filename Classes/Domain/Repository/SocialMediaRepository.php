<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\ZernioConnector\Domain\Repository;

use JAKOTA\ZernioConnector\Domain\Model\SocialMedia;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for SocialMedia posts.
 *
 * @extends \TYPO3\CMS\Extbase\Persistence\Repository<SocialMedia>
 */
class SocialMediaRepository extends Repository {
  public function findAll() {
    $query = $this->createQuery();

    $query->setOrderings([
      'publish_date' => QueryInterface::ORDER_DESCENDING,
    ]);

    return $query->execute();
  }

  /**
   * @return QueryResultInterface<int, SocialMedia>
   */
  public function findByTypeAndMedia(string $platform, int $limit): QueryResultInterface {
    $query = $this->createQuery();

    $query->matching(
      $query->logicalAnd(
        $query->equals('type', $platform),
        $query->greaterThan('media', 0)
      )
    );

    $query->setOrderings([
      'publishDate' => QueryInterface::ORDER_DESCENDING,
    ]);

    $query->setLimit($limit);

    return $query->execute();
  }

  public function findOneByPostIdIncludingHidden(string $postId): ?SocialMedia {
    $query = $this->createQuery();

    $query->getQuerySettings()->setIgnoreEnableFields(true);

    return $query
      ->matching(
        $query->equals('post_id', $postId)
      )
      ->setLimit(1)
      ->execute()
      ->getFirst()
    ;
  }
}

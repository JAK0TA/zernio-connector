<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\ZernioConnector\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class SocialMedia extends AbstractEntity {
  protected int $comments = 0;

  protected string $content = '';

  protected \DateTime $crdate;

  protected int $likes = 0;

  protected string $link = '';

  protected ?FileReference $media = null;

  protected string $mediaHash = '';

  protected string $postId = '';

  protected ?\DateTime $publishDate = null;

  protected string $type = '';

  public function getComments(): int {
    return $this->comments;
  }

  public function getContent(): string {
    return $this->content;
  }

  public function getCrdate(): \DateTime {
    return $this->crdate;
  }

  public function getLikes(): int {
    return $this->likes;
  }

  public function getLink(): string {
    return $this->link;
  }

  public function getMedia(): ?FileReference {
    return $this->media;
  }

  public function getMediaHash(): string {
    return $this->mediaHash;
  }

  public function getPostId(): string {
    return $this->postId;
  }

  public function getPublishDate(): ?\DateTime {
    return $this->publishDate;
  }

  public function getType(): string {
    return $this->type;
  }

  public function setComments(int $comments): self {
    $this->comments = $comments;

    return $this;
  }

  public function setContent(string $content): self {
    $this->content = $content;

    return $this;
  }

  public function setCrdate(\DateTime $crdate): self {
    $this->crdate = $crdate;

    return $this;
  }

  public function setLikes(int $likes): self {
    $this->likes = $likes;

    return $this;
  }

  public function setLink(string $link): self {
    $this->link = $link;

    return $this;
  }

  public function setMedia(?FileReference $media): self {
    $this->media = $media;

    return $this;
  }

  public function setMediaHash(string $mediaHash): self {
    $this->mediaHash = $mediaHash;

    return $this;
  }

  public function setPostId(string $postId): self {
    $this->postId = $postId;

    return $this;
  }

  public function setPublishDate(?\DateTime $publishDate): self {
    $this->publishDate = $publishDate;

    return $this;
  }

  public function setType(string $type): self {
    $this->type = $type;

    return $this;
  }
}

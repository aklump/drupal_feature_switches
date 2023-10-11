<?php

namespace Drupal\feature_switches;

class Feature {

  private $id = '';

  private $isLive = FALSE;

  private $isReady = FALSE;

  private $description = '';

  public function __construct(string $id) {
    $this->setId($id);
  }

  public static function create(string $id) {
    return new self($id);
  }

  public function getId(): string {
    return $this->id;
  }

  public function setId(string $id): self {
    $this->id = $id;

    return $this;
  }

  public function isLive(): bool {
    return $this->isLive;
  }

  public function setIsLive(bool $isLive): self {
    $this->isLive = $isLive;

    return $this;
  }

  public function isReady(): bool {
    return $this->isReady;
  }

  public function setIsReady(bool $isReady): self {
    $this->isReady = $isReady;

    return $this;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setDescription(string $description): self {
    $this->description = $description;

    return $this;
  }

  public function __toString(): string {
    return rtrim($this->getId() . ': ' . $this->getDescription(), ' :');
  }

}

<?php

namespace Drupal\feature_switches;

/**
 * Provide access to a Switchboard instance.
 */
final class Operator implements \JsonSerializable {

  /**
   * @var \Drupal\feature_switches\Switchboard
   */
  private $switchboard;

  /**
   * @var int
   */
  private $options;

  public function __construct(Switchboard $switchboard, int $options = 0) {
    $this->switchboard = $switchboard;
    $this->options = $options;
  }

  public function getOptions(): int {
    return $this->options;
  }

  public function setOptions(int $options): self {
    $this->options = $options;

    return $this;
  }

  public function get(string $id): Feature {
    if (!static::has($id)) {
      return new Feature($id);
    }

    return $this->switchboard[$id];
  }

  public function add(Feature $feature): self {
    if (static::has($feature)) {
      throw new FeatureAlreadyAddedException($feature);
    }
    if (!$this->options & FeatureSwitchOptions::ALLOW_UNREADY_LIVE && !$feature->isReady() && $feature->isLive()) {
      throw new FeatureNotReadyException($feature);
    }
    $this->switchboard[$feature->getId()] = $feature;

    return $this;
  }

  public function has($feature_or_id): bool {
    if ($feature_or_id instanceof Feature) {
      $feature_or_id = $feature_or_id->getId();
    }

    return isset($this->switchboard[$feature_or_id]);
  }

  public function jsonSerialize() {
    $data = [];
    foreach ($this->switchboard as $item) {
      /** @var $item \Drupal\feature_switches\Feature */
      $data[$item->getId()] = [
        'id' => $item->getId(),
        'description' => $item->getDescription(),
        'ready' => $item->isReady(),
        'live' => $item->isLive(),
      ];
    }

    return $data;
  }
}

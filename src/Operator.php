<?php

namespace Drupal\feature_switches;

/**
 * Provide access to a Switchboard instance.
 */
final class Operator {

  /**
   * @var \Drupal\feature_switches\Switchboard
   */
  private $switchboard;

  public function __construct(Switchboard $switchboard) {
    $this->switchboard = $switchboard;
  }

  public function get(string $id): Feature {
    if (!static::has($id)) {
      return new Feature($id);
    }

    return $this->switchboard[$id];
  }

  public function add(Feature $feature): self {
    if (static::has($feature)) {
      throw new \InvalidArgumentException(sprintf('The feature you are trying to add already exists in the switchboard: %s', $feature));
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

}

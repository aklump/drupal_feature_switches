<?php

namespace Drupal\feature_switches;

/**
 * The main API class.
 */
class FeatureSwitches {

  /**
   * @var \Drupal\feature_switches\Switchboard|null
   */
  static private $sharedList;

  public static function global(): Operator {
    if (!isset(self::$sharedList)) {
      self::$sharedList = new Operator(new Switchboard());
    }

    return self::$sharedList;
  }

  public static function isLive(string $feature): bool {
    return self::global()->get($feature)->isLive();
  }

}

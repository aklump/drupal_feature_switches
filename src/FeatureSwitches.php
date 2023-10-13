<?php

namespace Drupal\feature_switches;

/**
 * Singleton class for a global Operator instance.
 *
 * Not all Operator methods are represented, so if you need something just use
 * ::getOperator() and call the missing method.  Most useful methods have been
 * added as static methods for better DX.
 */
class FeatureSwitches {

  /**
   * @var \Drupal\feature_switches\Operator;
   */
  static private $sharedList;

  public static function getOperator(): Operator {
    if (!isset(self::$sharedList)) {
      self::$sharedList = new Operator(new Switchboard());
    }

    return self::$sharedList;
  }

  public static function setOptions(int $options): Operator {
    return self::getOperator()->setOptions($options);
  }

  public static function get(string $id): Feature {
    return self::getOperator()->get($id);
  }

  public static function isLive(string $feature): bool {
    return self::getOperator()->get($feature)->isLive();
  }

  public static function has($feature_or_id): bool {
    return self::getOperator()->has($feature_or_id);
  }

}

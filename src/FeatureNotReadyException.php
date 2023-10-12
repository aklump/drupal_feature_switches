<?php

namespace Drupal\feature_switches;

class FeatureNotReadyException extends \UnexpectedValueException {

  public function __construct(Feature $feature) {
    $message = sprintf('Unready feature cannot be live: %s; see also %s', $feature, '\Drupal\feature_switches\Operator::OPTION_UNREADY_CAN_BE_LIVE');
    parent::__construct($message, 1);
  }
}

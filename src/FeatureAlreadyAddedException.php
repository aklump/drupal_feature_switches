<?php

namespace Drupal\feature_switches;

class FeatureAlreadyAddedException extends \OverflowException {

  public function __construct(Feature $feature) {
    $message = sprintf('This feature already exists in the switchboard: %s.', $feature);
    parent::__construct($message, 1);
  }
}

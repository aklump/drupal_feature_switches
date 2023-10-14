<!--
id: changelog
tags: ''
-->

# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

* None

## [0.0.5] - 2023-10-13

### Changed

- `FeatureSwitchOptions::ALLOW_UNREADY_LIVE` -> `OperatorOptions::REQUIRE_READY_LIVE` and inverted logic.
- The default behavior is now to allow unready to be live. You must set the option for the exception to throw.

## [0.0.4] - 2023-10-13

### Added

- `\Drupal\feature_switches\FeatureSwitches::get()`
- `\Drupal\feature_switches\FeatureSwitches::has()`
- `\Drupal\feature_switches\FeatureSwitches::setOptions()`

### Changed

- `\Drupal\feature_switches\FeatureSwitches::global()` renamed to `\Drupal\feature_switches\FeatureSwitches::getOperator()`
  

# Feature Switches Drupal Module

## Summary

## Installation

1. Download this module to _web/modules/custom/feature_switches_.
1. Add the following to the application's _composer.json_ above web root.

    ```json
    {
      "repositories": [
        {
          "type": "path",
          "url": "app/install/modules/feature_switches"
        }
      ]
    }
    ```

1. Now run `composer require aklump_drupal/feature-switches:@dev`
1. Enable this module.

## Configuration

Refer to _feature_switches.api.php_ for implementation details.

<!--
id: readme
tags: ''
-->

# Feature Switches Module

## Summary

Allows you to flag features as ready or not, live or not from a central "switchboard". Based on these switches your codebase can act one way or another.

## How to Define Feature Switches

1. Create a file in the same directory as _settings.php_. Call it _feature\_switches.php_.
2. Add to _settings.php_, this line `include_once __DIR__ . '/feature_switches.php';`.  **Note: you should add this AFTER `$config['system.logging']['error_level']` otherwise you may not see the expected error output, if your features are in error.**
3. Open _feature\_switches.php_ and add one or more features, like this:

```php
\Drupal\feature_switches\FeatureSwitches::getOperator()
  ->add(\Drupal\feature_switches\Feature::create('show_outlines')
    ->setDescription('Add outlines to all images.')
    ->setIsReady(TRUE)
    ->setIsLive(TRUE)
  )
  ->add(\Drupal\feature_switches\Feature::create('user_files_download')
    ->setDescription('Allow users to download their own backups.')
    ->setIsReady(TRUE)
    ->setIsLive(FALSE)
  );
```

> For dynamic switch values--such as those depending on the DI container--you will need to set those switches later in the bootstrap of Drupal, for example inside an event listener.

### Unready Features Cannot Be Live

This will be enforced unless you use the `FeatureSwitchOptions::ALLOW_UNREADY_LIVE` option like this:

```php
FeatureSwitches::setOptions(\Drupal\feature_switches\FeatureSwitchOptions::ALLOW_UNREADY_LIVE);
```

It has to be done before trying to add the unready, live feature, otherwise a `Drupal\feature_switches\FeatureNotReadyException` is thrown.

## Setting Switches Inside Event Listeners

If you have a switch that is dependent on the current user having, say, a given role, you will need to wait until that current user is loaded to calculate that value and set the switch since the container is not yet initialized in _settings.php_ when you defined the switch. So to do that, you can listen for the `\Symfony\Component\HttpKernel\KernelEvents::REQUEST` event, and then set the value accordingly.

It's possible to set a switch anywhere in your code, so this is just a tested suggestion. This event is the earliest point when the user is available, in the Drupal bootstrap.

When you do this you must have a custom module, where you can add the event listener.

And you must declare a dependency on the feature_switches module.

### The Event Listener Class

_my\_module/src/EventSubscriber/MyModuleFeatureSwitches.php_

```php
namespace Drupal\my_module\EventSubscriber;

use Drupal\feature_switches\FeatureSwitches;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MyModuleFeatureSwitches implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    // Best practice; use class_exists().
    // @link https://www.drupal.org/project/drupal/issues/2825358
    if (class_exists(KernelEvents::CLASS)) {
      $events[KernelEvents::REQUEST][] = ['setUserDependentFeatureSwitches', 0];
    }

    return $events;
  }

  /**
   * Respond to a new request event.
   *
   * @param RequestEvent $event
   *   A new event instance.
   */
  public function setUserDependentFeatureSwitches(RequestEvent $event) {
    $early_access = in_array('early_access', \Drupal::currentUser()
      ->getRoles(TRUE));
    FeatureSwitches::get('user_files_download')->setIsLive($early_access);
  }

}
```

> `FeatureSwitches::get('bogus')->setIsLive(TRUE)` will fail quietly, when `bogus` is not added. In other words `setIsLive()` will have no effect. If you call `FeatureSwitches::isLive('bogus)` it will return `FALSE`.

### Make a Service Class Entry

_my\_module.services.yml_

```yaml
services:
  my_module.feature_switches:
    class: \Drupal\my_module\EventSubscriber\MyModuleFeatureSwitches
    tags: [ { name: event_subscriber } ]

```

### Declare Module Dependency

_my\_module.info.yml_

```yaml
dependencies:
  - feature_switches:feature_switches

```

## Using Your Feature Switches

The whole point of this module to is allow your codebase to react differently based on a feature being live or not. It's quite simple once your features have been created to check them.

### Do Something When the Feature Is Live

```php
if (\Drupal\feature_switches\FeatureSwitches::isLive('user_files_download')) {
  // Proceed with the process...
}
```

### Access the Feature Info

```php
/** @var \Drupal\feature_switches\Feature $foo_feature */
$download_feature = \Drupal\feature_switches\FeatureSwitches::get('download');
$download_feature->getId();
$download_feature->getDescription();
$download_feature->isReady();
$download_feature->isLive();
```

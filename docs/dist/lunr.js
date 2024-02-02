var lunrIndex = [{"id":"changelog","title":"Changelog","body":"All notable changes to this project will be documented in this file.\n\nThe format is based on [Keep a Changelog](https:\/\/keepachangelog.com\/en\/1.0.0\/),\nand this project adheres to [Semantic Versioning](https:\/\/semver.org\/spec\/v2.0.0.html).\n\n## [Unreleased]\n\n* None\n\n## [0.0.5] - 2023-10-13\n\n### Changed\n\n- `FeatureSwitchOptions::ALLOW_UNREADY_LIVE` -> `OperatorOptions::REQUIRE_READY_LIVE` and inverted logic.\n- The default behavior is now to allow unready to be live. You must set the option for the exception to throw.\n\n## [0.0.4] - 2023-10-13\n\n### Added\n\n- `\\Drupal\\feature_switches\\FeatureSwitches::get()`\n- `\\Drupal\\feature_switches\\FeatureSwitches::has()`\n- `\\Drupal\\feature_switches\\FeatureSwitches::setOptions()`\n\n### Changed\n\n- `\\Drupal\\feature_switches\\FeatureSwitches::global()` renamed to `\\Drupal\\feature_switches\\FeatureSwitches::getOperator()`"},{"id":"developers","title":"Developers","body":"## PHPUnit Testing\n\n```php\n  public static function setUpBeforeClass(): void {\n\n    \/\/ Setup the operator her with your feature switches.\n    FeatureSwitches::getOperator()\n      ->add(Feature::create('mailchimp')\n        ->setIsReady(TRUE)\n      );\n  }\n  public function testSomethign() {\n\n    \/\/ turn it on and off in a test.\n    FeatureSwitches::get('mailchimp')->setIsLive($feature_switch);\n\n  }\n```"},{"id":"readme","title":"Feature Switches Drupal Module","body":"## Summary\n\nAllows you to flag features as ready\/not ready and live\/not live from a central \"switchboard\". Based on these switches your codebase can act one way or another.\n\n## Install with Composer\n\n1. Because this is an unpublished package, you must define it's repository in your project's _composer.json_ file. Add the following to _composer.json_:\n\n    ```json\n    \"repositories\": [\n        {\n            \"type\": \"github\",\n            \"url\": \"https:\/\/github.com\/aklump\/drupal_feature_switches\"\n        }\n    ]\n    ```\n\n1. Then `composer require aklump_drupal\/feature_switches:^0.0`\n1. It will be installed to _web\/modules\/custom\/feature_switches_, which should be excluded from source control.\n\n1. Enable this module.\n2. See section below about declaring as a Drupal dependency.\n\n## How to Define Feature Switches\n\n1. Create a file in the same directory as _settings.php_. Call it _feature\\_switches.php_.\n2. Add to _settings.php_, the following.  **Note: you should add this AFTER `$config['system.logging']['error_level']` otherwise you may not see the expected error output, if your features are in error.**\n\n    ```php\n    include_once __DIR__ . '\/feature_switches.php';\n    ```\n3. Open _feature\\_switches.php_ and add one or more features, like this:\n\n```php\n\\Drupal\\feature_switches\\FeatureSwitches::getOperator()\n  ->add(\\Drupal\\feature_switches\\Feature::create('show_outlines')\n    ->setDescription('Add outlines to all images.')\n    ->setIsReady(TRUE)\n    ->turnOn()\n  )\n  ->add(\\Drupal\\feature_switches\\Feature::create('user_files_download')\n    ->setDescription('Allow users to download their own backups.')\n    ->setIsReady(TRUE)\n    ->turnOff()\n  );\n```\n\n> For dynamic switch values--such as those depending on the DI container--you will need to set those switches later in the bootstrap of Drupal, for example inside an event listener.\n\n### Enforcing All Live Features are Also Ready\n\nTo require that any live feature must also be marked as ready, set the `\\Drupal\\feature_switches\\OperatorOptions::REQUIRE_READY_LIVE` option. Doing so will cause a `\\Drupal\\feature_switches\\FeatureNotReadyException` to be thrown if you try to add a feature that is live but not ready.\n\n```php\nFeatureSwitches::setOptions(\\Drupal\\feature_switches\\OperatorOptions::REQUIRE_READY_LIVE);\n```\n\nThis has to be done before adding features, otherwise no exceptions are thrown.\n\n## Setting Switches Inside Event Listeners\n\nIf you have a switch that is dependent on the current user having, say, a given role, you will need to wait until that current user is loaded to calculate that value and set the switch since the container is not yet initialized in _settings.php_ when you defined the switch. So to do that, you can listen for the `\\Symfony\\Component\\HttpKernel\\KernelEvents::REQUEST` event, and then set the value accordingly.\n\nIt's possible to set a switch anywhere in your code, so this is just a tested suggestion. This event is the earliest point when the user is available, in the Drupal bootstrap.\n\nWhen you do this you must have a custom module, where you can add the event listener.\n\nAnd you must declare a dependency on the feature_switches module.\n\n### The Event Listener Class\n\n_my\\_module\/src\/EventSubscriber\/MyModuleFeatureSwitches.php_\n\n```php\nnamespace Drupal\\my_module\\EventSubscriber;\n\nuse Drupal\\feature_switches\\FeatureSwitches;\nuse Symfony\\Component\\HttpKernel\\KernelEvents;\nuse Symfony\\Component\\HttpKernel\\Event\\RequestEvent;\nuse Symfony\\Component\\EventDispatcher\\EventSubscriberInterface;\n\nclass MyModuleFeatureSwitches implements EventSubscriberInterface {\n\n  \/**\n   * {@inheritdoc}\n   *\/\n  public static function getSubscribedEvents() {\n    $events = [];\n    \/\/ Best practice; use class_exists().\n    \/\/ @link https:\/\/www.drupal.org\/project\/drupal\/issues\/2825358\n    if (class_exists(KernelEvents::CLASS)) {\n      $events[KernelEvents::REQUEST][] = ['setUserDependentFeatureSwitches', 0];\n    }\n\n    return $events;\n  }\n\n  \/**\n   * Respond to a new request event.\n   *\n   * @param RequestEvent $event\n   *   A new event instance.\n   *\/\n  public function setUserDependentFeatureSwitches(RequestEvent $event) {\n    $early_access = in_array('early_access', \\Drupal::currentUser()\n      ->getRoles(TRUE));\n    FeatureSwitches::get('user_files_download')->setIsLive($early_access);\n  }\n\n}\n```\n\n> `FeatureSwitches::get('bogus')->turnOn()` will fail quietly, when `bogus` is not added. In other words `setIsLive()` will have no effect. If you call `FeatureSwitches::isLive('bogus)` it will return `FALSE`.\n\n### Make a Service Class Entry\n\n_my\\_module.services.yml_\n\n```yaml\nservices:\n  my_module.feature_switches:\n    class: \\Drupal\\my_module\\EventSubscriber\\MyModuleFeatureSwitches\n    tags: [ { name: event_subscriber } ]\n\n```\n\n### Declare Module Dependency\n\n_my\\_module.info.yml_\n\n```yaml\ndependencies:\n  - feature_switches:feature_switches\n\n```\n\n## Using Your Feature Switches\n\nThe whole point of this module to is allow your codebase to react differently based on a feature being live or not. Once your features have been created, it's quite simple to check them.\n\n### Do Something When the Feature Is Live\n\n```php\nif (\\Drupal\\feature_switches\\FeatureSwitches::isLive('user_files_download')) {\n  \/\/ Proceed with the process...\n}\n```\n\n### Access the Feature Info\n\n```php\n\/** @var \\Drupal\\feature_switches\\Feature $foo_feature *\/\n$download_feature = \\Drupal\\feature_switches\\FeatureSwitches::get('download');\n$download_feature->getId();\n$download_feature->getDescription();\n\n\/\/ Note: these two are synonymous.\n$download_feature->isReady();\n$download_feature->isLive();\n```"}]
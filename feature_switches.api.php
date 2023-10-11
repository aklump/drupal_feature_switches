<?php
/**
 * @file
 * Defines the API functions provided by the feature_switches module.
 */

/**
 * Here's how to add a new feature.  This should be done in settings.php.  You
 * do not need to chain each feature, as each call to
 * \Drupal\feature_switches\FeatureList::get returns the same instance.
 */

\Drupal\feature_switches\FeatureList::global()
  ->add(\Drupal\feature_switches\Feature::create('foo')
    ->setDescription('Some foo.')
    ->setIsReady(TRUE)
    ->setIsLive(FALSE)
  )
  ->add(\Drupal\feature_switches\Feature::create('bar')
    ->setDescription('Some bar.')
    ->setIsReady(TRUE)
    ->setIsLive(TRUE)
  );

\Drupal\feature_switches\FeatureList::global()
  ->add(\Drupal\feature_switches\Feature::create('baz')
    ->setDescription('Some baz.')
    ->setIsReady(FALSE)
    ->setIsLive(FALSE)
  );

/**
 * Here is an example of how you will conditional run some code based on if the
 * feature "foo" is live or not.  There are two syntaxes for this.s
 */
if (\Drupal\feature_switches\FeatureList::isLive('foo')) {
  // TODO Something.
}
if (\Drupal\feature_switches\FeatureList::global()->get('foo')->isLive()) {
  // TODO Something.
}

/**
 * If you need to get the feature object from the global list.
 */
/** @var \Drupal\feature_switches\Feature $foo_feature */
$foo_feature = \Drupal\feature_switches\FeatureList::global()->get('foo');

/**
 * To check if a feature is ready, there is no shorthand so do like this:
 */
$is_ready = \Drupal\feature_switches\FeatureList::global()
  ->get('foo')
  ->isReady();

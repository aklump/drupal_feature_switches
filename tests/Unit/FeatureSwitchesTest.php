<?php

namespace Drupal\Tests\feature_switches;

use Drupal\feature_switches\Feature;
use Drupal\feature_switches\FeatureSwitchOptions;
use Drupal\feature_switches\Operator;
use Drupal\feature_switches\FeatureSwitches;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Drupal\feature_switches\FeatureSwitches
 * @uses   \Drupal\feature_switches\Operator
 * @uses   \Drupal\feature_switches\Feature
 */
class FeatureSwitchesTest extends TestCase {

  public function testGlobalSetOptionsSetsOnExistingGlobalOperator() {
    $switches = FeatureSwitches::global();
    FeatureSwitches::global()->setOptions(FeatureSwitchOptions::ALLOW_UNREADY_LIVE);
    $this->assertSame($switches, FeatureSwitches::global());
    $this->assertSame(1, $switches->getOptions() & FeatureSwitchOptions::ALLOW_UNREADY_LIVE);
  }

  public function testSetIsLiveOnNonExistentHasNoEffect() {
    FeatureSwitches::global()->get('bogus')->setIsLive(TRUE);
    $this->assertFalse(FeatureSwitches::isLive('bogus'));
  }

  public function testIsLiveReturnsTrueForExistingLiveFeature() {
    FeatureSwitches::global()->add(
      Feature::create('bar')
        ->setIsReady(TRUE)
        ->setIsLive(TRUE)
    );
    $this->assertTrue(FeatureSwitches::isLive('bar'));
  }

  public function testIsLiveReturnsFalseForNonLiveExistent() {
    FeatureSwitches::global()->add(Feature::create('foo'));
    $this->assertFalse(FeatureSwitches::isLive('foo'));
  }

  public function testIsLiveReturnsFalseForNonExistent() {
    $this->assertFalse(FeatureSwitches::isLive('foo'));
  }

  public function testGetReturnsSameInstanceCalledMultipleTimes() {
    $foo = FeatureSwitches::global();
    $this->assertSame($foo, FeatureSwitches::global());
    $this->assertInstanceOf(Operator::class, $foo);
  }

}

<?php

namespace Drupal\Tests\feature_switches;

use Drupal\feature_switches\Feature;
use Drupal\feature_switches\OperatorOptions;
use Drupal\feature_switches\Operator;
use Drupal\feature_switches\FeatureSwitches;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Drupal\feature_switches\FeatureSwitches
 * @uses   \Drupal\feature_switches\Operator
 * @uses   \Drupal\feature_switches\Feature
 */
class FeatureSwitchesTest extends TestCase {

  public function testHasReturnsAsExpected() {
    $this->assertFalse(FeatureSwitches::has('firmament'));
    $feature = Feature::create('firmament');
    FeatureSwitches::getOperator()->add($feature);
    $this->assertTrue(FeatureSwitches::has('firmament'));
    $this->assertTrue(FeatureSwitches::has($feature));
  }

  public function testSetOptionsSetsOnExistingGlobalOperator() {
    $switches = FeatureSwitches::getOperator();
    $this->assertSame($switches, FeatureSwitches::setOptions(OperatorOptions::REQUIRE_READY_LIVE));
    $this->assertSame($switches, FeatureSwitches::getOperator());
    $this->assertSame(1, $switches->getOptions() & OperatorOptions::REQUIRE_READY_LIVE);
  }

  public function testSetIsLiveOnNonExistentHasNoEffect() {
    FeatureSwitches::get('bogus')->setIsLive(TRUE);
    $this->assertFalse(FeatureSwitches::isLive('bogus'));
  }

  public function testIsLiveReturnsTrueForExistingLiveFeature() {
    $operator = FeatureSwitches::getOperator();
    $this->assertSame($operator, FeatureSwitches::getOperator()
      ->add(
        Feature::create('bar')
          ->setIsReady(TRUE)
          ->setIsLive(TRUE)
      ));
    $this->assertTrue(FeatureSwitches::isLive('bar'));
  }

  public function testIsLiveReturnsFalseForNonLiveExistent() {
    FeatureSwitches::getOperator()->add(Feature::create('foo'));
    $this->assertFalse(FeatureSwitches::isLive('foo'));
  }

  public function testIsLiveReturnsFalseForNonExistent() {
    $this->assertFalse(FeatureSwitches::isLive('foo'));
  }

  public function testGetReturnsSameInstanceCalledMultipleTimes() {
    $foo = FeatureSwitches::getOperator();
    $this->assertSame($foo, FeatureSwitches::getOperator());
    $this->assertInstanceOf(Operator::class, $foo);
  }

}

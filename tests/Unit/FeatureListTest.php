<?php

namespace Drupal\Tests\feature_switches;

use Drupal\feature_switches\Feature;
use Drupal\feature_switches\Operator;
use Drupal\feature_switches\FeatureList;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Drupal\feature_switches\FeatureList
 * @uses   \Drupal\feature_switches\Operator
 * @uses   \Drupal\feature_switches\Feature
 */
class FeatureListTest extends TestCase {

  public function testSetIsLiveOnNonExistentHasNoEffect() {
    FeatureList::global()->get('bogus')->setIsLive(TRUE);
    $this->assertFalse(FeatureList::isLive('bogus'));
  }

  public function testIsLiveReturnsFalseForLiveExistent() {
    FeatureList::global()->add(Feature::create('bar')->setIsLive(TRUE));
    $this->assertTrue(FeatureList::isLive('bar'));
  }

  public function testIsLiveReturnsFalseForNonLiveExistent() {
    FeatureList::global()->add(Feature::create('foo'));
    $this->assertFalse(FeatureList::isLive('foo'));
  }

  public function testIsLiveReturnsFalseForNonExistent() {
    $this->assertFalse(FeatureList::isLive('foo'));
  }

  public function testGetReturnsSameInstanceCalledMultipleTimes() {
    $foo = FeatureList::global();
    $this->assertSame($foo, FeatureList::global());
    $this->assertInstanceOf(Operator::class, $foo);
  }

}

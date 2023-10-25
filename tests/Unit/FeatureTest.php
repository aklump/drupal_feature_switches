<?php

namespace Drupal\Tests\feature_switches;

use Drupal\feature_switches\Feature;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Drupal\feature_switches\Feature
 */
class FeatureTest extends TestCase {

  public function testCreateReturnsNewInstance() {
    $foo = Feature::create('foo');
    $this->assertNotSame($foo, Feature::create('foo'));
  }

  public function testCanSetIsReadyFalse() {
    $feature = new Feature('bar');
    $this->assertTrue($feature->setIsReady(TRUE)->isReady());
    $this->assertFalse($feature->setIsReady(FALSE)->isReady());
  }

  public function testCanSetIsReadyTrue() {
    $this->assertTrue((new Feature('bar'))->setIsReady(TRUE)->isReady());
  }

  public function testIsReadyFalseByDefault() {
    $this->assertFalse((new Feature('bar'))->isReady());
  }

  public function testCanTurnOff() {
    $feature = new Feature('foo');
    $this->assertTrue($feature->turnOn()->isLive());
    $this->assertFalse($feature->turnOff()->isLive());
  }

  public function testCanTurnOn() {
    $this->assertTrue((new Feature('foo'))->turnOn()->isLive());
  }

  public function testIsLiveFalseByDefault() {
    $this->assertFalse((new Feature('foo'))->isLive());
  }

  public function testToString() {
    $feature = new Feature('extended_members_only');
    $this->assertSame('extended_members_only', (string) $feature);
    $feature->setDescription('Limit login to users with extended member role only.');
    $this->assertSame('extended_members_only: Limit login to users with extended member role only.', (string) $feature);
  }

  public function testConstructor() {
    $feature = new Feature('extended_members_only');
    $this->assertInstanceOf(Feature::class, $feature);
  }
}

<?php

namespace Drupal\Tests\feature_switches;

use Drupal\feature_switches\Feature;
use Drupal\feature_switches\FeatureNotReadyException;
use Drupal\feature_switches\OperatorOptions;
use Drupal\feature_switches\Operator;
use Drupal\feature_switches\Switchboard;
use PHPUnit\Framework\TestCase;

/**
 * @covers   \Drupal\feature_switches\Operator
 * @covers   \Drupal\feature_switches\FeatureAlreadyAddedException
 * @covers   \Drupal\feature_switches\FeatureNotReadyException
 * @uses     \Drupal\feature_switches\Feature
 * @uses     \Drupal\feature_switches\OperatorOptions
 */
class OperatorTest extends TestCase {

  public function testGetAndSetOptionsWorkAsExpected() {
    $operator = new Operator(new Switchboard());
    $operator->setOptions(OperatorOptions::REQUIRE_READY_LIVE);
    $this->assertSame(1, $operator->getOptions() & OperatorOptions::REQUIRE_READY_LIVE);
  }

  public function testNotReadyTurnOnWorksByDefault() {
    $operator = new Operator(new Switchboard());
    $operator->add(Feature::create('foo_not_ready')
      ->setIsReady(FALSE)->turnOn()
    );
    $this->assertFalse($operator->get('foo_not_ready')->isReady());
    $this->assertTrue($operator->get('foo_not_ready')->isLive());
  }

  public function testNotReadyTurnOnThrowsWhenUsingRequireReadyLiveOption() {
    $operator = new Operator(new Switchboard(), OperatorOptions::REQUIRE_READY_LIVE);
    $this->expectException(FeatureNotReadyException::class);
    $this->expectExceptionMessageMatches('/OperatorOptions::REQUIRE_READY_LIVE/');
    $operator->add(Feature::create('foo_not_ready')
      ->setIsReady(FALSE)->turnOn()
    );
  }

  public function testJsonSerializeReturnsExpectedArray() {
    $operator = new Operator(new Switchboard());
    $operator
      ->add(Feature::create('foo')
        ->setDescription('Lorem foo.')
        ->setIsReady(FALSE)
        ->turnOff()
      )
      ->add(Feature::create('bar')
        ->setDescription('Lorem bar.')
        ->setIsReady(TRUE)
        ->turnOff()
      )->add(Feature::create('baz')
        ->setDescription('Lorem baz.')
        ->setIsReady(TRUE)
        ->turnOn()
      );

    $data = $operator->jsonSerialize();
    $this->assertCount(3, $data);

    $this->assertSame('Lorem foo.', $data['foo']['description']);
    $this->assertFalse($data['foo']['ready']);
    $this->assertFalse($data['foo']['live']);

    $this->assertSame('Lorem bar.', $data['bar']['description']);
    $this->assertTrue($data['bar']['ready']);
    $this->assertFalse($data['bar']['live']);

    $this->assertSame('Lorem baz.', $data['baz']['description']);
    $this->assertTrue($data['baz']['ready']);
    $this->assertTrue($data['baz']['live']);
  }


  public function testJsonSerializeReturnsFooElementWithExpectedKeys() {
    $operator = new Operator(new Switchboard());
    $operator->add(Feature::create('alpha'));
    $data = $operator->jsonSerialize();
    $this->assertCount(1, $data);
    $this->assertArrayHasKey('alpha', $data);
    $this->assertArrayHasKey('id', $data['alpha']);
    $this->assertArrayHasKey('description', $data['alpha']);
    $this->assertArrayHasKey('ready', $data['alpha']);
    $this->assertArrayHasKey('live', $data['alpha']);
  }

  public function testAddingAgainThrows() {
    $switchboard = new Switchboard();
    $feature = new Feature('foo');
    (new Operator($switchboard))->add($feature);
    $this->expectException(\Drupal\feature_switches\FeatureAlreadyAddedException::class);
    (new Operator($switchboard))->add($feature);
  }

  public function testSwitchboardPersistsAcrossOperators() {
    $switchboard = new Switchboard();
    $feature = new Feature('foo');
    $feature->turnOn()
      ->setIsReady(TRUE)
      ->setDescription('Lorem foo.');
    (new Operator($switchboard))->add($feature);
    $operator2 = new Operator($switchboard);
    $this->assertTrue($operator2->has('foo'));
    $this->assertTrue($operator2->get('foo')->isLive());
    $this->assertTrue($operator2->get('foo')->isReady());
    $this->assertSame('Lorem foo.', $operator2->get('foo')->getDescription());
  }

  public function testHasReturnsTrueForExisting() {
    $operator = new Operator(new Switchboard());
    $this->assertTrue($operator->add(new Feature('foo'))
      ->has('foo'));
  }

  public function testHasReturnsFalseForNonExistent() {
    $operator = new Operator(new Switchboard());
    $this->assertFalse($operator->has('foo'));
  }

  public function testIsEnabledFalseForNonExistentFeature() {
    $this->assertFalse((new Operator(new Switchboard()))->get('lorem')
      ->isLive());
  }

}

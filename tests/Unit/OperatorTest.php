<?php

namespace Drupal\Tests\feature_switches;

use Drupal\feature_switches\Feature;
use Drupal\feature_switches\Operator;
use Drupal\feature_switches\Switchboard;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Drupal\feature_switches\Operator
 * @uses   \Drupal\feature_switches\Feature
 */
class OperatorTest extends TestCase {


  public function testJsonSerializeReturnsExpectedArray() {
    $operator = new Operator(new Switchboard());
    $operator
      ->add(Feature::create('foo')
        ->setDescription('Lorem foo.')
        ->setIsReady(FALSE)
        ->setIsLive(FALSE)
      )
      ->add(Feature::create('bar')
        ->setDescription('Lorem bar.')
        ->setIsReady(TRUE)
        ->setIsLive(FALSE)
      )->add(Feature::create('baz')
        ->setDescription('Lorem baz.')
        ->setIsReady(TRUE)
        ->setIsLive(TRUE)
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
    $this->assertArrayHasKey('description', $data['alpha']);
    $this->assertArrayHasKey('ready', $data['alpha']);
    $this->assertArrayHasKey('live', $data['alpha']);
  }

  public function testAddingAgainThrows() {
    $switchboard = new Switchboard();
    $feature = new Feature('foo');
    (new Operator($switchboard))->add($feature);
    $this->expectException(\InvalidArgumentException::class);
    (new Operator($switchboard))->add($feature);
  }

  public function testSwitchboardPersistsAcrossOperators() {
    $switchboard = new Switchboard();
    $feature = new Feature('foo');
    $feature->setIsLive(TRUE)
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

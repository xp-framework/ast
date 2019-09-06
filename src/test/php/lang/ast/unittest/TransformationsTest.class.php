<?php namespace lang\ast\unittest;

use lang\ast\transform\Transformations;
use unittest\TestCase;

class TransformationsTest extends TestCase {
  private $remove= [];

  /**
   * Registers a transformation; registering it for removal a test shutdown
   *
   * @param  string $kind
   * @param  function(lang.ast.Node): lang.ast.Node|iterable $function
   * @return void
   */
  private function register($kind, $function) {
    $this->remove[]= Transformations::register($kind, $function);
  }

  /**
   * Assertion helper
   *
   * @param  [:var][] $expected
   * @throws unittest.AssertionFailedError
   */
  private function assertRegistered($expected) {
    $actual= [];
    foreach (Transformations::registered() as $kind => $transformation) {
      $actual[]= [$kind => $transformation];
    }
    $this->assertEquals($expected, $actual);
  }

  /** @return void */
  public function tearDown() {
    Transformations::remove(...$this->remove);
  }

  #[@test]
  public function registered_initially_empty() {
    $this->assertRegistered([]);
  }

  #[@test]
  public function register_function() {
    $function= function($class) { return $class; };

    $this->register('class', $function);
    $this->assertRegistered([['class' => $function]]);
  }

  #[@test]
  public function register_two_functions() {
    $first= function($class) { return $class; };
    $second= function($class) { $class->annotations['author']= 'Test'; return $class; };

    $this->register('class', $first);
    $this->register('class', $second);
    $this->assertRegistered([['class' => $first], ['class' => $second]]);
  }
}
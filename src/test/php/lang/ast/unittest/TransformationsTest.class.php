<?php namespace lang\ast\unittest;

use lang\ast\transform\Transformations;
use unittest\{Assert, Test};

class TransformationsTest {

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
    Assert::equals($expected, $actual);
  }

  #[Test]
  public function registered_initially_empty() {
    $this->assertRegistered([]);
  }

  #[Test]
  public function register_function() {
    $function= function($codegen, $class) { return $class; };

    $remove= Transformations::register('class', $function);
    try {
      $this->assertRegistered([['class' => $function]]);
    } finally {
      Transformations::remove($remove);
    }
  }

  #[Test]
  public function register_two_functions() {
    $first= function($codegen, $class) { return $class; };
    $second= function($codegen, $class) { $class->annotations['author']= 'Test'; return $class; };

    $remove= [Transformations::register('class', $first), Transformations::register('class', $second)];
    try {
      $this->assertRegistered([['class' => $first], ['class' => $second]]);
    } finally {
      Transformations::remove(...$remove);
    }
  }
}
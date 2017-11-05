<?php namespace lang\ast\unittest;

use lang\ast\transform\Transformations;

class TransformationsTest extends \unittest\TestCase {

  #[@test]
  public function register() {
    Transformations::register('class', function($class) {
      return $class;
    });
  }
}
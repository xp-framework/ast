<?php namespace lang\ast\unittest;

use lang\ast\nodes\ClassValue;
use lang\ast\nodes\Annotation;

class AnnotationsTest extends \unittest\TestCase {

  #[@test]
  public function no_annotation() {
    $this->assertNull((new ClassValue('Test', [], null, [], [], [], null))->annotation('value'));
  }

  #[@test]
  public function annotation_without_value() {
    $this->assertEquals(
      new Annotation('value', null),
      (new ClassValue('Test', [], null, [], [], ['value' => null], null))->annotation('value')
    );
  }
}
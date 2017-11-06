<?php namespace lang\ast\unittest;

use lang\ast\nodes\ClassDeclaration;
use lang\ast\nodes\Annotation;

class AnnotationsTest extends \unittest\TestCase {

  #[@test]
  public function no_annotation() {
    $this->assertNull((new ClassDeclaration([], 'Test', null, [], [], []))->annotation('value'));
  }

  #[@test]
  public function annotation_without_value() {
    $this->assertEquals(
      new Annotation('value', null),
      (new ClassDeclaration([], 'Test', null, [], [], ['value' => null]))->annotation('value')
    );
  }
}
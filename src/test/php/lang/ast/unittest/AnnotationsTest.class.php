<?php namespace lang\ast\unittest;

use lang\ast\nodes\{Annotation, ClassDeclaration};
use unittest\Assert;

class AnnotationsTest {

  #[@test]
  public function no_annotation() {
    Assert::null((new ClassDeclaration([], 'Test', null, [], []))->annotation('value'));
  }

  #[@test]
  public function annotation_without_value() {
    Assert::equals(
      new Annotation('value', null),
      (new ClassDeclaration([], 'Test', null, [], ['value' => null]))->annotation('value')
    );
  }
}
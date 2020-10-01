<?php namespace lang\ast\unittest;

use lang\ast\nodes\{Annotation, ClassDeclaration};
use unittest\{Assert, Test};

class AnnotationsTest {

  #[Test]
  public function no_annotation() {
    Assert::null((new ClassDeclaration([], 'Test', null, [], [], []))->annotation('value'));
  }

  #[Test]
  public function annotation_without_value() {
    Assert::equals(
      new Annotation('value', null),
      (new ClassDeclaration([], 'Test', null, [], [], ['value' => null]))->annotation('value')
    );
  }
}
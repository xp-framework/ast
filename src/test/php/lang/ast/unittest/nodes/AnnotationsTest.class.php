<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\{Annotation, Annotations, Literal};
use test\{Assert, Test};

class AnnotationsTest extends NodeTest {

  #[Test]
  public function empty_list() {
    Assert::equals([], (new Annotations())->all());
  }

  #[Test]
  public function with_annotation() {
    $test= new Annotation('Test', null);
    Assert::equals(['Test' => $test], (new Annotations($test))->all());
  }

  #[Test]
  public function with_mapped_annotation() {
    $test= new Annotation('Test', null);
    Assert::equals(['Test' => $test], (new Annotations(['Test' => $test]))->all());
  }

  #[Test]
  public function with_mapped_value() {
    $test= new Annotation('Test', null);
    Assert::equals(['Test' => $test], (new Annotations(['Test' => null]))->all());
  }

  #[Test]
  public function named() {
    $test= new Annotation('Test', null);
    Assert::equals($test, (new Annotations($test))->named('Test'));
  }

  #[Test]
  public function no_annotation_named() {
    Assert::null((new Annotations())->named('Test'));
  }

  #[Test]
  public function add() {
    $test= new Annotation('Test', null);
    $annotations= new Annotations();
    $annotations->add($test);
    Assert::equals(['Test' => $test], $annotations->all());
  }

  #[Test]
  public function remove() {
    $test= new Annotation('Test', null);
    $annotations= new Annotations($test);
    $annotations->remove($test);
    Assert::equals([], $annotations->all());
  }

  #[Test]
  public function remove_by_name() {
    $test= new Annotation('Test', null);
    $annotations= new Annotations($test);
    $annotations->remove('Test');
    Assert::equals([], $annotations->all());
  }

  #[Test]
  public function iteration() {
    $test= new Annotation('Test', null);
    Assert::equals(['Test' => null], iterator_to_array(new Annotations($test)));
  }

  #[Test]
  public function children() {
    $test= new Annotation('Test', null);
    Assert::equals([$test], iterator_to_array((new Annotations($test))->children()));
  }
}
<?php namespace lang\ast\unittest;

use lang\ast\nodes\{Annotation, Annotations, ClassDeclaration};
use unittest\{Assert, Test, Values};

class AnnotationsTest {

  #[Test]
  public function can_create() {
    new Annotations();
  }

  #[Test]
  public function initially_empty() {
    Assert::equals([], (new Annotations())->all());
  }

  #[Test]
  public function non_existant() {
    Assert::null((new Annotations())->named(Test::class));
  }

  #[Test]
  public function all() {
    Assert::equals(
      [Test::class => new Annotation(Test::class, [])],
      (new Annotations([Test::class => []]))->all()
    );
  }

  #[Test]
  public function iteration() {
    Assert::equals(
      [Test::class => []],
      iterator_to_array(new Annotations([Test::class => []]))
    );
  }

  #[Test]
  public function named() {
    Assert::equals(
      new Annotation(Test::class, []),
      (new Annotations([Test::class => []]))->named(Test::class)
    );
  }

  #[Test]
  public function add() {
    Assert::equals(
      [Test::class => new Annotation(Test::class, [])],
      (new Annotations())->add(new Annotation(Test::class, []))->all()
    );
  }

  #[Test]
  public function remove() {
    $fixture= new Annotations([Test::class => []]);
    $fixture->remove(Test::class);
    Assert::equals([], $fixture->all());
  }

  #[Test]
  public function removing_returns_removed_annotation() {
    $fixture= new Annotations([Test::class => []]);
    Assert::equals(new Annotation(Test::class, []), $fixture->remove(Test::class));
  }

  #[Test]
  public function removing_returns_null_if_nothing_as_rempved() {
    Assert::null((new Annotations())->remove(Test::class));
  }

  #[Test]
  public function no_annotations_from_declaration() {
    Assert::equals([], (new ClassDeclaration([], 'Test', null, [], [], null))->annotations());
  }

  #[Test]
  public function no_annotation_from_declaration() {
    Assert::null((new ClassDeclaration([], 'Test', null, [], [], null))->annotation(Test::class));
  }

  #[Test]
  public function annotation_from_declaration() {
    Assert::equals(
      new Annotation(Test::class, []),
      (new ClassDeclaration([], 'Test', null, [], [], new Annotations([Test::class => []])))->annotation(Test::class)
    );
  }

  #[Test]
  public function annotate_declaration() {
    $declaration= new ClassDeclaration([], 'Test', null, [], [], new Annotations([Test::class => []]));
    Assert::equals(
      [Test::class => new Annotation(Test::class, []), Values::class => new Annotation(Values::class, [])],
      $declaration->annotate(new Annotation(Values::class, []))->annotations()
    );
  }

  #[Test]
  public function annotate_declaration_without_annotations() {
    $declaration= new ClassDeclaration([], 'Test', null, [], [], null);
    Assert::equals(
      [Test::class => new Annotation(Test::class, [])],
      $declaration->annotate(new Annotation(Test::class, []))->annotations()
    );
  }
}
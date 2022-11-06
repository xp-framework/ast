<?php namespace lang\ast\unittest;

use lang\ast\nodes\{Annotation, Annotations, ClassDeclaration};
use lang\ast\types\IsValue;
use unittest\{Assert, Before, Test, Values};

class AnnotationsTest {
  private $annotation;

  #[Before]
  public function annotation() {
    $this->annotation= new Annotation(Test::class, []);
  }

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
      [Test::class => $this->annotation],
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
  public function with_annotation() {
    Assert::equals(
      $this->annotation,
      (new Annotations($this->annotation))->named(Test::class)
    );
  }

  #[Test]
  public function with_named() {
    Assert::equals(
      $this->annotation,
      (new Annotations([Test::class => []]))->named(Test::class)
    );
  }

  #[Test]
  public function with_annotations() {
    Assert::equals(
      $this->annotation,
      (new Annotations([Test::class => $this->annotation]))->named(Test::class)
    );
  }

  #[Test]
  public function add() {
    Assert::equals(
      [Test::class => $this->annotation],
      (new Annotations())->add($this->annotation)->all()
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
    Assert::equals($this->annotation, $fixture->remove(Test::class));
  }

  #[Test]
  public function removing_returns_null_if_nothing_as_rempved() {
    Assert::null((new Annotations())->remove(Test::class));
  }

  #[Test]
  public function no_annotations_from_declaration() {
    Assert::equals([], (new ClassDeclaration([], new IsValue('T'), null, [], [], null))->annotations());
  }

  #[Test]
  public function no_annotation_from_declaration() {
    Assert::null((new ClassDeclaration([], new IsValue('T'), null, [], [], null))->annotation(Test::class));
  }

  #[Test]
  public function annotation_from_declaration() {
    Assert::equals(
      $this->annotation,
      (new ClassDeclaration([], new IsValue('T'), null, [], [], new Annotations([Test::class => []])))->annotation(Test::class)
    );
  }

  #[Test]
  public function annotate_declaration() {
    $declaration= new ClassDeclaration([], new IsValue('T'), null, [], [], new Annotations([Test::class => []]));
    Assert::equals(
      [Test::class => $this->annotation, Values::class => new Annotation(Values::class, [])],
      $declaration->annotate(new Annotation(Values::class, []))->annotations()
    );
  }

  #[Test]
  public function annotate_declaration_without_annotations() {
    $declaration= new ClassDeclaration([], new IsValue('T'), null, [], [], null);
    Assert::equals(
      [Test::class => $this->annotation],
      $declaration->annotate($this->annotation)->annotations()
    );
  }

  #[Test]
  public function set_declarations_annotations() {
    $declaration= new ClassDeclaration([], new IsValue('T'), null, [], [], null);
    Assert::equals(
      [Test::class => $this->annotation],
      $declaration->annotate(new Annotations([Test::class => []]))->annotations()
    );
  }
}
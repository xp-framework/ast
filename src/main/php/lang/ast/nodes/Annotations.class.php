<?php namespace lang\ast\nodes;

use IteratorAggregate, Traversable;
use lang\ast\Node;

class Annotations extends Node implements IteratorAggregate {
  public $named= [];
  public $kind= 'annotations';

  /**
   * Creates annotations
   *
   * @param  lang.ast.nodes.Annotation|[:lang.ast.nodes.Annotation|var[]] $arg
   * @param  int $line
   */
  public function __construct($arg= [], $line= -1) {
    if ($arg instanceof Annotation) {
      $this->named[$arg->name]= $arg;
    } else foreach ($arg as $name => $value) {
      $this->named[$name]= $value instanceof Annotation ? $value : new Annotation($name, $value, $line);
    }
    $this->line= $line;
  }

  /** Adds a given annotation */
  public function add(Annotation $annotation): self {
    $this->named[$annotation->name]= $annotation;
    return $this;
  }

  /**
   * Remove an annotation and return it, if any.
   *
   * @param  string|lang.ast.nodes.Annotation $target
   * @return ?lang.ast.nodes.Annotation
   */
  public function remove($target) {
    $name= $target instanceof Annotation ? $target->name : $target;
    if ($removed= $this->named[$name] ?? null) {
      unset($this->named[$name]);
      return $removed;
    }
    return null;
  }

  /**
   * Returns an annotation by its name
   *
   * @param  string $name
   * @return ?lang.ast.nodes.Annotation
   */
  public function named($name) {
    return $this->named[$name] ?? null;
  }

  /**
   * Returns all annotations by their name
   *
   * @return [:lang.ast.nodes.Annotation]
   */
  public function all() {
    return $this->named;
  }

  /** Iterates annotation arguments mapped by their names */
  public function getIterator(): Traversable {
    foreach ($this->named as $name => $annotation) {
      yield $name => $annotation->arguments;
    }
  }

  /** @return iterable */
  public function children() {
    foreach ($this->named as $annotation) {
      yield $annotation;
    }
  }
}
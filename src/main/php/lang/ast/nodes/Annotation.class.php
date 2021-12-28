<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Annotation extends Node {
  public $name, $arguments;
  public $kind= 'annotation';

  public function __construct($name, $arguments, $line= -1) {
    $this->name= $name;
    $this->arguments= $arguments;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->arguments; }
}
<?php namespace lang\ast\nodes;

use lang\ast\Node;

class NewClassExpression extends Node {
  public $kind= 'newclass';
  public $definition, $arguments;

  public function __construct($definition, $arguments, $line= -1) {
    $this->definition= $definition;
    $this->arguments= $arguments;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->arguments; }
}
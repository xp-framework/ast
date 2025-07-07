<?php namespace lang\ast\nodes;

use lang\ast\Node;

class CallableExpression extends Node {
  public $kind= 'callable';
  public $expression;
  public $arguments;

  public function __construct($expression, $arguments= [], $line= -1) {
    $this->expression= $expression;
    $this->arguments= $arguments;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [&$this->expression]; }
}
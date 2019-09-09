<?php namespace lang\ast\nodes;

use lang\ast\Node;

class InvokeExpression extends Node {
  public $kind= 'invoke';
  public $expression, $arguments;

  public function __construct($expression, $arguments, $line= -1) {
    $this->expression= $expression;
    $this->arguments= $arguments;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    yield $this->expression;
    foreach ($this->arguments as $element) {
      yield $element;
    }
  }
}
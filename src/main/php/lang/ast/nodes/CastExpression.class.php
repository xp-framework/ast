<?php namespace lang\ast\nodes;

use lang\ast\Node;

class CastExpression extends Node {
  public $kind= 'cast';
  public $type, $expression;

  public function __construct($type, $expression, $line= -1) {
    $this->type= $type;
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->expression]; }
}
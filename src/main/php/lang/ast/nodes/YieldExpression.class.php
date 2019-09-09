<?php namespace lang\ast\nodes;

use lang\ast\Node;

class YieldExpression extends Node {
  public $kind= 'yield';
  public $key, $value;

  public function __construct($key, $value, $line= -1) {
    $this->key= $key;
    $this->value= $value;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    return $this->key ? [$this->key, $this->value] : [$this->value];
  }
}
<?php namespace lang\ast\nodes;

class ForeachLoop extends Value {
  public $kind= 'foreach';
  public $expression, $key, $value, $body;

  public function __construct($expression, $key, $value, $body, $line= -1) {
    $this->expression= $expression;
    $this->key= $key;
    $this->value= $value;
    $this->body= $body;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    yield $this->expression;
    if ($this->key) {
      yield $this->key;
    }
    yield $this->value;
    foreach ($this->body as $element) {
      yield $element;
    }
  }
}
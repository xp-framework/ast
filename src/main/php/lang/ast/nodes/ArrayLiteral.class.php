<?php namespace lang\ast\nodes;

class ArrayLiteral extends Value {
  public $kind= 'array';
  public $values;

  public function __construct($values, $line= -1) {
    $this->values= $values;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    foreach ($this->values as $pair) {
      yield $pair[0];
      yield $pair[1];
    }
  }
}
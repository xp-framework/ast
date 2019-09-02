<?php namespace lang\ast\nodes;

class Annotations extends Value {
  public $kind= 'annotation';
  public $values;

  public function __construct($values, $line= -1) {
    $this->values= $values;
    $this->line= $line;
  }
}
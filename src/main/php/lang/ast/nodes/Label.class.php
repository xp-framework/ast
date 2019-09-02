<?php namespace lang\ast\nodes;

class Label extends Value {
  public $kind= 'label';
  public $name;

  public function __construct($name, $line= -1) {
    $this->name= $name;
    $this->line= $line;
  }
}
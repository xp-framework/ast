<?php namespace lang\ast\nodes;

class Variable extends Value {
  public $kind= 'variable';
  public $name;

  public function __construct($name, $line= -1) {
    $this->name= $name;
    $this->line= $line;
  }
}
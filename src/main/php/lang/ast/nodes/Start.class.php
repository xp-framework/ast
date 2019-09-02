<?php namespace lang\ast\nodes;

class Start extends Value {
  public $kind= 'start';
  public $syntax;

  public function __construct($syntax, $line= -1) {
    $this->syntax= $syntax;
    $this->line= $line;
  }
}
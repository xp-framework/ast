<?php namespace lang\ast\nodes;

class StaticLocals extends Value {
  public $kind= 'static';
  public $initializations;

  public function __construct($initializations= null, $line= -1) {
    $this->initializations= $initializations;
    $this->line= $line;
  }
}
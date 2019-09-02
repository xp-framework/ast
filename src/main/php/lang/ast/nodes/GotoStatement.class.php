<?php namespace lang\ast\nodes;

class GotoStatement extends Value {
  public $kind= 'goto';
  public $label;

  public function __construct($label, $line= -1) {
    $this->label= $label;
    $this->line= $line;
  }
}
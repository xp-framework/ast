<?php namespace lang\ast\nodes;

use lang\ast\Node;

class GotoStatement extends Node {
  public $kind= 'goto';
  public $label;

  public function __construct($label, $line= -1) {
    $this->label= $label;
    $this->line= $line;
  }
}
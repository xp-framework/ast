<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Directives extends Node {
  public $kind= 'directives';
  public $declare;

  public function __construct($declare, $line= -1) {
    $this->declare= $declare;
    $this->line= $line;
  }
}
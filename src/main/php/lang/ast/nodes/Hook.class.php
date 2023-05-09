<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Hook extends Node {
  public $kind= 'hook';
  public $type, $field, $expression, $argument;

  public function __construct($type, $field, $expression, $argument= null, $line= -1) {
    $this->type= $type;
    $this->field= $field;
    $this->expression= $expression;
    $this->argument= $argument;
    $this->line= $line;
  }
}
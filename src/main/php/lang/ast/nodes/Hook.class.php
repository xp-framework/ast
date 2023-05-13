<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Hook extends Node {
  public $kind= 'hook';
  public $modifiers, $type, $field, $expression, $parameter, $holder;

  public function __construct($modifiers, $type, $field, $expression, $parameter= null, $line= -1, $holder= null) {
    $this->modifiers= $modifiers;
    $this->type= $type;
    $this->field= $field;
    $this->expression= $expression;
    $this->parameter= $parameter;
    $this->line= $line;
    $this->holder= $holder;
  }
}
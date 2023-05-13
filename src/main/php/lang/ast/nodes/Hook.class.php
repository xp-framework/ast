<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Hook extends Node {
  public $kind= 'hook';
  public $modifiers, $type, $field, $expression, $parameter;

  public function __construct($modifiers, $type, $field, $expression, $parameter= null, $line= -1) {
    $this->modifiers= $modifiers;
    $this->type= $type;
    $this->field= $field;
    $this->expression= $expression;
    $this->parameter= $parameter;
    $this->line= $line;
  }
}
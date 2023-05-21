<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Hook extends Node {
  public $kind= 'hook';
  public $modifiers, $type, $expression, $byref, $parameter, $holder;

  public function __construct($modifiers, $type, $expression, $byref= false, $parameter= null, $line= -1, $holder= null) {
    $this->modifiers= $modifiers;
    $this->type= $type;
    $this->expression= $expression;
    $this->byref= $byref;
    $this->parameter= $parameter;
    $this->line= $line;
    $this->holder= $holder;
  }
}
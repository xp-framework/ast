<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Constant extends Node implements Member {
  public $kind= 'const';
  public $name, $modifiers, $expression, $type;

  public function __construct($modifiers, $name, $type, $expression, $line= -1) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->type= $type;
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return string */
  public function lookup() { return $this->name; }
}
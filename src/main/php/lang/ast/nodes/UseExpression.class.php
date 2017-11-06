<?php namespace lang\ast\nodes;

class UseExpression extends Value {
  public $types, $aliases;

  public function __construct($types, $aliases) {
    $this->types= $types;
    $this->aliases= $aliases;
  }
}
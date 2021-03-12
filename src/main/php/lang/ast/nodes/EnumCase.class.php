<?php namespace lang\ast\nodes;

use lang\ast\Node;

class EnumCase extends Node implements Member {
  public $kind= 'enumcase';
  public $name, $expression;

  public function __construct($name, $expression, $line= -1) {
    $this->name= $name;
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return string */
  public function lookup() { return $this->name; }
}
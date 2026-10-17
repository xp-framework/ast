<?php namespace lang\ast\nodes;

class Generic extends Literal {
  public $kind= 'generic';
  public $expression, $components;

  public function __construct($expression, $components, $line= -1) {
    $this->expression= $expression;
    $this->components= $components;
    $this->line= $line;
  }
}
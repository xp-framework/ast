<?php namespace lang\ast\nodes;

class DoValue extends Value {
  public $expression, $body;

  public function __construct($expression, $body) {
    $this->expression= $expression;
    $this->body= $body;
  }
}
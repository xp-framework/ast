<?php namespace lang\ast\nodes;

class CaseLabel extends Value {
  public $kind= 'case';
  public $expression, $body;

  public function __construct($expression, $body) {
    $this->expression= $expression;
    $this->body= $body;
  }

  /** @return iterable */
  public function children() { return [$this->expression]; }
}
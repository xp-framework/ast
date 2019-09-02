<?php namespace lang\ast\nodes;

class ContinueStatement extends Value {
  public $kind= 'continue';
  public $expression;

  public function __construct($expression= null, $line= -1) {
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    return $this->expression ? [$this->expression] : [];
  }
}
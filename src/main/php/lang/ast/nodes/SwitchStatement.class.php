<?php namespace lang\ast\nodes;

class SwitchStatement extends Value {
  public $kind= 'switch';
  public $expression, $cases;

  public function __construct($expression, $cases, $line= -1) {
    $this->expression= $expression;
    $this->cases= $cases;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    yield $this->expression;
    foreach ($this->cases as $element) {
      yield $element;
    }
  }
}
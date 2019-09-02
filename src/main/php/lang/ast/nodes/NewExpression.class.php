<?php namespace lang\ast\nodes;

class NewExpression extends Value {
  public $kind= 'new';
  public $type, $arguments;

  public function __construct($type, $arguments, $line= -1) {
    $this->type= $type;
    $this->arguments= $arguments;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->arguments; }
}
<?php namespace lang\ast\nodes;

class UseStatement extends Value {
  public $kind= 'import';
  public $type, $names;

  public function __construct($type, $names, $line= -1) {
    $this->type= $type;
    $this->names= $names;
    $this->line= $line;
  }
}
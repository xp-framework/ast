<?php namespace lang\ast;

class Code implements Element {
  public $kind= 'code';
  public $line= -1;
  public $value;

  public function __construct($value= '') {
    $this->value= $value;
  }

  public function append($value) {
    $this->value.= $value;
  }
}
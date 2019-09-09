<?php namespace lang\ast;

class Code extends Node {
  public $kind= 'code';
  public $value;

  public function __construct($value= '') {
    $this->value= $value;
  }

  public function append($value) {
    $this->value.= $value;
  }
}
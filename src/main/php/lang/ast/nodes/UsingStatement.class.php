<?php namespace lang\ast\nodes;

class UsingStatement extends Value {
  public $arguments, $body;

  public function __construct($arguments, $body) {
    $this->arguments= $arguments;
    $this->body= $body;
  }
}
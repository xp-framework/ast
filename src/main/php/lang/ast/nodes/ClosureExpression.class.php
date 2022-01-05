<?php namespace lang\ast\nodes;

class ClosureExpression extends Annotated {
  public $kind= 'closure';
  public $static, $signature, $use, $body;

  public function __construct($static, $signature, $use, $body, $line= -1) {
    $this->static= $static;
    $this->signature= $signature;
    $this->use= $use;
    $this->body= $body;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->body; }
}
<?php namespace lang\ast\nodes;

class ClosureExpression extends Annotated {
  public $kind= 'closure';
  public $static, $signature, $use, $body;

  public function __construct($signature, $use, $body, $static= false, $line= -1) {
    $this->signature= $signature;
    $this->use= $use;
    $this->body= is_array($body) ? new Block($body, $line) : $body;
    $this->static= $static;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [&$this->body]; }
}
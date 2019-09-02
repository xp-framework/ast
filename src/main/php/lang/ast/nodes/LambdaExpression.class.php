<?php namespace lang\ast\nodes;

class LambdaExpression extends Value {
  public $kind= 'lambda';
  public $signature, $body;

  public function __construct($signature, $body, $line= -1) {
    $this->signature= $signature;
    $this->body= $body;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    return is_array($this->body) ? $this->body : [$this->body];
  }
}
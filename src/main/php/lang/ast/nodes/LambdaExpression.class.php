<?php namespace lang\ast\nodes;

class LambdaExpression extends Annotated {
  public $kind= 'lambda';
  public $static, $signature, $body;

  public function __construct($signature, $body, $static= false, $line= -1) {
    $this->signature= $signature;
    $this->body= $body;
    $this->static= $static;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    return is_array($this->body) ? $this->body : [$this->body];
  }
}
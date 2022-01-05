<?php namespace lang\ast\nodes;

class LambdaExpression extends Annotated {
  public $kind= 'lambda';
  public $static, $signature, $body;

  public function __construct($static, $signature, $body, $line= -1) {
    $this->static= $static;
    $this->signature= $signature;
    $this->body= $body;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    return is_array($this->body) ? $this->body : [$this->body];
  }
}
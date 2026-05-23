<?php namespace lang\ast\nodes;

class LambdaExpression extends Annotated {
  public $kind= 'lambda';
  public $static, $signature, $body;

  public function __construct($signature, $body, $static= false, $line= -1) {
    $this->signature= $signature;
    $this->body= is_array($body) ? new Block($body, $line) : $body;
    $this->static= $static;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [&$this->body]; }
}
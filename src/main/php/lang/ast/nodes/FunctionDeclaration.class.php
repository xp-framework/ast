<?php namespace lang\ast\nodes;

class FunctionDeclaration extends Annotated {
  public $kind= 'function';
  public $name, $signature, $body;

  public function __construct($name, $signature, $body, $line= -1) {
    $this->name= $name;
    $this->signature= $signature;
    $this->body= $body;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->body; }
}
<?php namespace lang\ast\nodes;

class MethodValue extends Annotated implements Member {
  public $name, $modifiers, $signature, $annotations, $body, $comment;

  public function __construct($name, $modifiers, $signature, $annotations, $body, $comment) {
    $this->name= $name;
    $this->modifiers= $modifiers;
    $this->signature= $signature;
    $this->annotations= $annotations;
    $this->body= $body;
    $this->comment= $comment;
  }

  /** @return string */
  public function kind() { return 'method'; }

  /** @return string */
  public function lookup() { return $this->name.'()'; }
}
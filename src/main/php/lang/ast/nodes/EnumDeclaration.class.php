<?php namespace lang\ast\nodes;

use lang\ast\nodes\TypeDeclaration;

class EnumDeclaration extends TypeDeclaration {
  public $kind= 'enum';
  public $base, $implements;

  public function __construct($modifiers, $name, $base, $implements= [], $body= [], $annotations= [], $comment= null, $line= -1) {
    parent::__construct($modifiers, $name, $body, $annotations, $comment, $line);
    $this->implements= $implements;
    $this->base= $base;
  }

  public function interfaces() { return $this->implements; }

  public function case($name) { return $this->body[$name] ?? null; }
}
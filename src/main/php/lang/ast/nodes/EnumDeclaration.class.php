<?php namespace lang\ast\nodes;

use lang\ast\nodes\TypeDeclaration;

class EnumDeclaration extends TypeDeclaration {
  public $kind= 'enum';
  public $implements;

  public function __construct($modifiers, $name, $implements= [], $body= [], $annotations= [], $comment= null, $line= -1) {
    parent::__construct($modifiers, $name, $body, $annotations, $comment, $line);
    $this->implements= $implements;
  }

  public function interfaces() { return $this->implements; }
}
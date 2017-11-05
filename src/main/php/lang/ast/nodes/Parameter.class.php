<?php namespace lang\ast\nodes;

class Parameter extends Annotated {
  public $name, $reference, $type, $variadic, $promote, $default, $annotations;

  public function __construct($name, $type, $default= null, $reference= false, $variadic= false, $promote= null, $annotations= []) {
    $this->name= $name;
    $this->type= $type;
    $this->default= $default;
    $this->reference= $reference;
    $this->variadic= $variadic;
    $this->promote= $promote;
    $this->annotations= $annotations;
  }
}
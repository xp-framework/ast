<?php namespace lang\ast\nodes;

class Parameter extends Annotated {
  public $kind= 'parameter';
  public $name, $reference, $type, $variadic, $promote, $default, $annotations;

  public function __construct($name, $type, $default= null, $reference= false, $variadic= false, $promote= null, $annotations= null) {
    $this->name= $name;
    $this->type= $type;
    $this->default= $default;
    $this->reference= $reference;
    $this->variadic= $variadic;
    $this->promote= $promote;
    $this->annotations= $annotations;
  }

  /**
   * Checks whether this node is of a given kind
   *
   * @param  string $kind
   * @return bool
   */
  public function is($kind) {
    return $this->kind === $kind || parent::is($kind);
  }
}
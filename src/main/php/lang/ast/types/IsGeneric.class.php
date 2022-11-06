<?php namespace lang\ast\types;

use lang\ast\Type;

class IsGeneric extends Type {
  public $base, $components;

  /**
   * Creates a new type
   *
   * @param  parent $base
   * @param  parent[] $components
   */
  public function __construct($base, $components= []) {
    $this->base= $base;
    $this->components= $components;
  }

  /** @return string */
  public function literal() { return literal($this->name()); }

  /** @return string */
  public function name() {
    $n= '';
    foreach ($this->components as $type) {
      $n.= ', '.$type->name();
    }
    return strtr(ltrim($this->base, '\\'), '\\', '.').'<'.substr($n, 2).'>';
  }
}
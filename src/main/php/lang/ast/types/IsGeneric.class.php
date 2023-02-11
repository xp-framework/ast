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
  public function literal() {
    $c= '';
    foreach ($this->components as $type) {
      $c.= ', '.$type->name();
    }
    return literal($this->base.'<'.substr($c, 2).'>');
  }

  /** @return string */
  public function name() {
    $c= '';
    foreach ($this->components as $type) {
      $c.= ', '.$type->name();
    }
    return strtr(ltrim($this->base, '\\'), '\\', '.').'<'.substr($c, 2).'>';
  }
}
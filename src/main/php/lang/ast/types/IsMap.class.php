<?php namespace lang\ast\types;

use lang\ast\Type;

class IsMap extends Type {
  public $key, $value;

  /**
   * Creates a new type
   *
   * @param  parent $key
   * @param  parent $value
   */
  public function __construct($key, $value) {
    $this->key= $key;
    $this->value= $value;
  }

  /** @return string */
  public function literal() { return 'array'; }

  /** @return string */
  public function name() { return '[:'.$this->value->name().']'; }
}
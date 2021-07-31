<?php namespace lang\ast\types;

use lang\ast\Type;

class IsArray extends Type {
  public $component;

  /**
   * Creates a new type
   *
   * @param  parent $component
   */
  public function __construct($component) {
    $this->component= $component;
  }

  /** @return string */
  public function literal() { return 'array'; }

  /** @return string */
  public function name() { return $this->component->name().'[]'; }

}
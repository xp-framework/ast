<?php namespace lang\ast\types;

use lang\ast\Type;

class IsUnchecked extends Type {
  public $element;

  /**
   * Creates a new element
   *
   * @param  parent $element
   */
  public function __construct(Type $element) {
    $this->element= $element;
  }

  /** @return string */
  public function literal() { return $this->element->literal(); }

  /** @return string */
  public function name() { return $this->element->name(); }

  /**
   * Compare
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? $this->element->compareTo($value->element) : 1;
  }
}
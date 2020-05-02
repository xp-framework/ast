<?php namespace lang\ast;

class VariadicType extends Type {
  public $element;

  /**
   * Creates a new element
   *
   * @param  $element
   */
  public function __construct(Type $element) {
    $this->element= $element;
  }

  /** @return string */
  public function literal() { $this->element->literal().'...'; }

  /** @return string */
  public function name() { return $this->element->name().'...'; }

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
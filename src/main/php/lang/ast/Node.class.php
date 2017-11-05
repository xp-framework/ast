<?php namespace lang\ast;

use util\Objects;

class Node implements Element, \lang\Value {
  public $symbol, $value, $kind;
  public $line= -1;

  /** @param lang.ast.Symbol */
  public function __construct(Symbol $symbol= null, $kind= null, $value= null) {
    $this->symbol= $symbol;
    $this->kind= $kind;
    $this->value= $value;
  }

  /**
   * NUD - null denotation
   *
   * @return lang.ast.Node
   */
  public function nud() {
    return $this->symbol->nud
      ? $this->symbol->nud->__invoke($this)
      : $this
    ;
  }

  /**
   * LED - left denotation
   *
   * @param  lang.ast.Node $left
   * @return lang.ast.Node
   */
  public function led($left) {
    return $this->symbol->led
      ? $this->symbol->led->__invoke($this, $left)
      : $this
    ;
  }

  /**
   * STD - statement denotation
   *
   * @return lang.ast.Node
   */
  public function std() {
    return $this->symbol->std ? $this->symbol->std->__invoke($this) : null;
  }

  /** @return string */
  public function hashCode() {
    return $this->symbol->hashCode().$this->kind.Objects::hashOf($this->value);
  }

  /** @return string */
  public function toString() {
    return nameof($this).'(kind= '.$this->kind.', value= '.Objects::stringOf($this->value).')';
  }

  /**
   * Compares this node to another given value
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value === $this ? 0 : 1;
  }
}
<?php namespace lang\ast;

use lang\Value;
use util\Objects;

/** A single token as returned by the Tokens class */
class Token implements Value {
  public $symbol, $value, $kind, $line;
  public $comment= null;

  /**
   * Creates a new node
   *
   * @param  ?lang.ast.Symbol $symbol
   * @param  string $kind
   * @param  var $value
   * @param  int $line
   */
  public function __construct(?Symbol $symbol= null, $kind= null, $value= null, $line= -1) {
    $this->symbol= $symbol;
    $this->kind= $kind;
    $this->value= $value;
    $this->line= $line;
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
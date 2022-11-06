<?php namespace lang\ast\types;

use lang\IllegalStateException;
use lang\ast\Type;
use util\Objects;

class IsExpression extends Type {
  public $expression;

  /**
   * Creates a new type
   *
   * @param  lang.ast.Node $expression
   */
  public function __construct($expression) {
    $this->expression= $expression;
  }

  /** @return string */
  public function name() {
    throw new IllegalStateException('Expressions cannot be used as type name');
  }

  /** @return string */
  public function literal() {
    throw new IllegalStateException('Expressions cannot be used as type literal');
  }

  /** @return string */
  public function toString() { return nameof($this).'({'.nameof($this->expression).'})'; }

  /** @return string */
  public function hashCode() { return '{'.spl_object_hash($this->expression); }

  /**
   * Compare
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof static ? Objects::compare($this->expression, $value->expression) : 1;
  }
}
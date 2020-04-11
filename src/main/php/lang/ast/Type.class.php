<?php namespace lang\ast;

use lang\Value;

/**
 * Represents a type
 *
 * @test  xp://lang.ast.unittest.TypeTest
 */
class Type implements Value {
  public $literal;

  /** @param string $literal */
  public function __construct($literal) { $this->literal= $literal; }

  /** @return string */
  public function literal() { return $this->literal; }

  /** @return string */
  public function name() {
    static $map= ['mixed' => 'var'];

    $name= strtr(ltrim($this->literal, '?\\'), '\\', '.');
    isset($map[$name]) && $name= $map[$name];
    return '?' === $this->literal[0] ? '?'.$name : $name;
  }

  /** @return string */
  public function toString() { return nameof($this).'('.$this->name().')'; }

  /** @return string */
  public function hashCode() { return crc32($this->name()); }

  /**
   * Compare
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? $this->name() <=> $value->name() : 1;
  }
}
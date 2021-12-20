<?php namespace lang\ast\nodes;

use ArrayAccess, ReturnTypeWillChange;
use lang\IllegalAccessException;
use lang\ast\Node;

/** Comment */
class Comment extends Node implements ArrayAccess {
  public $kind= 'comment';
  public $declaration;

  /**
   * Creates a new comment
   *
   * @param  string $declaration including slashes and asterisks
   * @param  int $line
   */
  public function __construct($declaration= '', $line= -1) {
    $this->declaration= $declaration;
    $this->line= $line;
  }

  #[ReturnTypeWillChange]
  public function offsetExists($i) {
    return $i >= 0 && $i < strlen($this->declaration);
  }

  #[ReturnTypeWillChange]
  public function offsetGet($i) {
    return $this->declaration[$i] ?? null;
  }

  #[ReturnTypeWillChange]
  public function offsetSet($i, $value) {
    throw new IllegalAccessException('Cannot modify comment');
  }

  #[ReturnTypeWillChange]
  public function offsetUnset($i) {
    throw new IllegalAccessException('Cannot modify comment');
  }

  public function __toString() {
    return $this->declaration;
  }
}
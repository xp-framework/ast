<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Comment extends Node {
  public $kind= 'comment';
  public $declaration;

  /**
   * Creates a new comment
   *
   * @param string $declaration including slashes and asterisks
   * @param int $line
   */
  public function __construct($declaration= '', $line= -1) {
    $this->declaration= $declaration;
    $this->line= $line;
  }

  /**
   * Returns contents stripped of all slashes and asterisks.
   *
   * @return string
   */
  public function content() {
    return trim(preg_replace('/\n\s+\* ?/', "\n", substr($this->declaration, 3, -2)));
  }

  /** @return string */
  public function __toString() {
    return $this->declaration;
  }
}
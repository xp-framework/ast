<?php namespace lang\ast;

use lang\IllegalStateException;

/** A single errors in the `lang.ast.Errors` collection */
class Error extends IllegalStateException {

  /**
   * Creates a new error
   *
   * @param  string $message
   * @param  string $file
   * @param  int $line
   */
  public function __construct($message, $file, $line) {
    parent::__construct($message);
    $this->file= $file;
    $this->line= $line;
  }
}
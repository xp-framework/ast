<?php namespace lang\ast\nodes;

use lang\ast\Node;

/**
 * These two placeholder symbols exist:
 *
 * - The argument place holder `?` means that exactly one argument is
 *   expected at this position.
 * - The variadic place holder `...` means that zero or more arguments
 *   may be supplied at this position.
 * 
 * @see   https://wiki.php.net/rfc/partial_function_application_v2
 */
class Placeholder extends Node {
  public static $ARGUMENT, $VARIADIC;
  public $literal;
  public $kind= 'placeholder';

  static function __static() {
    self::$ARGUMENT= new self('?');
    self::$VARIADIC= new self('...');
  }

  /** @param string $literal */
  private function __construct($literal) {
    $this->literal= $literal;
  }
}
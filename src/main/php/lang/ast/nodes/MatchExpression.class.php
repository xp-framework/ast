<?php namespace lang\ast\nodes;

use lang\ast\Node;

/**
 * Match statement with cases
 *
 * @see  https://wiki.php.net/rfc/match_expression_v2
 * @test xp://lang.ast.unittest.ConditionalTest
 */
class MatchExpression extends Node {
  public $kind= 'match';
  public $expression, $cases, $default;

  public function __construct($expression, $cases, $default, $line= -1) {
    $this->expression= $expression;
    $this->cases= $cases;
    $this->default= $default;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    yield $this->expression;
    foreach ($this->cases as $element) {
      yield $element;
    }
  }
}
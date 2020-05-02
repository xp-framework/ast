<?php namespace lang\ast\unittest\parse;

use lang\ast\{Language, Node, Parse, Tokens};
use text\StringTokenizer;
use unittest\Assert;

abstract class ParseTest {
  const LINE = 1;

  /**
   * Parse code, returning nodes on at a time
   *
   * @param  string $code
   * @param  ?lang.ast.Scope $scope
   * @return lang.ast.Parse
   */
  protected function parse($code, $scope= null) {
    return new Parse(Language::named('PHP'), new Tokens(new StringTokenizer($code)), static::class, $scope);
  }

  /**
   * Assertion helper
   *
   * @param  [:var][] $expected
   * @param  iterable $nodes
   * @throws unittest.AssertionFailedError
   * @return void
   */
  protected function assertParsed($expected, $code) {
    $actual= [];
    foreach ($this->parse($code)->execute() as $node) {
      $actual[]= $node;
    }
    Assert::equals($expected, $actual);
  }
}
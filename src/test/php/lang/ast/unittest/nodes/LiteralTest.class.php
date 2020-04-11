<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\Literal;

class LiteralTest extends NodeTest {

  #[@test]
  public function can_create() {
    new Literal('true');
  }

  #[@test]
  public function expression() {
    $this->assertEquals('true', (new Literal('true'))->expression);
  }

  #[@test, @values([
  #  ['true', true],
  #  ['false', false],
  #  ['null', null],
  #  ['1', 1],
  #  ['1.5', 1.5],
  #  ['"test"', 'test'],
  #])]
  public function resolve($input, $expected) {
    $this->assertEquals($expected, (new Literal($input))->resolve());
  }
}
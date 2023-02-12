<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{Literal, OffsetExpression, StaticLocals, Variable};
use test\{Assert, Test, Values};

class VariablesTest extends ParseTest {

  #[Test, Values(['v', 'key', 'this', 'class', 'protected'])]
  public function variable($name) {
    $this->assertParsed(
      [new Variable($name, self::LINE)],
      '$'.$name.';'
    );
  }

  #[Test]
  public function static_variable() {
    $this->assertParsed(
      [new StaticLocals(['v' => null], self::LINE)],
      'static $v;'
    );
  }

  #[Test]
  public function static_variable_with_initialization() {
    $this->assertParsed(
      [new StaticLocals(['id' => new Literal('0', self::LINE)], self::LINE)],
      'static $id= 0;'
    );
  }

  #[Test]
  public function array_offset() {
    $this->assertParsed(
      [new OffsetExpression(new Variable('a', self::LINE), new Literal('0', self::LINE), self::LINE)],
      '$a[0];'
    );
  }
}
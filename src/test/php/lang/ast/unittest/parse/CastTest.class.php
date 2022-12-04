<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{CastExpression, Variable};
use lang\ast\types\{IsArray, IsLiteral, IsMap, IsNullable, IsValue, IsGeneric, IsFunction, IsUnion, IsIntersection};
use unittest\{Assert, Test, Values};

class CastTest extends ParseTest {

  /** @return iterable */
  private function types() {

    // Literals
    yield ['int', new IsLiteral('int')];
    yield ['float', new IsLiteral('float')];
    yield ['string', new IsLiteral('string')];
    yield ['bool', new IsLiteral('bool')];
    yield ['array', new IsLiteral('array')];
    yield ['object', new IsLiteral('object')];
    yield ['callable', new IsLiteral('callable')];
    yield ['iterable', new IsLiteral('iterable')];

    // Value types
    yield ['Value', new IsValue('Value')];
    yield ['\\lang\\Value', new IsValue('\\lang\\Value')];

    // Nullable
    yield ['?int', new IsNullable(new IsLiteral('int'))];
    yield ['?Value', new IsNullable(new IsValue('Value'))];

    // Functions
    yield ['function(): int', new IsFunction([], new IsLiteral('int'))];
    yield ['function(string): void', new IsFunction([new IsLiteral('string')], new IsLiteral('void'))];

    // Generic
    yield ['List<int>', new IsGeneric(new IsValue('List'), [new IsLiteral('int')])];
    yield ['Map<string, Value>', new IsGeneric(new IsValue('Map'), [new IsLiteral('string'), new IsValue('Value')])];
  }

  #[Test, Values('types')]
  public function simple($type, $expected) {
    $this->assertParsed(
      [new CastExpression($expected, new Variable('a', self::LINE), self::LINE)],
      '('.$type.')$a;'
    );
  }

  #[Test, Values('types')]
  public function array_of($type, $expected) {
    $this->assertParsed(
      [new CastExpression(new IsArray($expected), new Variable('a', self::LINE), self::LINE)],
      '(array<'.$type.'>)$a;'
    );
  }

  #[Test, Values('types')]
  public function map_of($type, $expected) {
    $this->assertParsed(
      [new CastExpression(new IsMap(new IsLiteral('string'), $expected), new Variable('a', self::LINE), self::LINE)],
      '(array<string, '.$type.'>)$a;'
    );
  }

  #[Test, Values('types')]
  public function union_of($type, $expected) {
    $this->assertParsed(
      [new CastExpression(new IsUnion([new IsLiteral('string'), $expected]), new Variable('a', self::LINE), self::LINE)],
      '(string|'.$type.')$a;'
    );
  }

  #[Test, Values('types')]
  public function intersection_of($type, $expected) {
    $this->assertParsed(
      [new CastExpression(new IsIntersection([new IsLiteral('string'), $expected]), new Variable('a', self::LINE), self::LINE)],
      '(string&'.$type.')$a;'
    );
  }
}
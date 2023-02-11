<?php namespace lang\ast\unittest\parse;

use lang\ast\types\{IsArray, IsFunction, IsGeneric, IsIntersection, IsLiteral, IsMap, IsNullable, IsUnion, IsValue};
use lang\ast\{Language, Parse, Tokens};
use test\{Assert, Test, Values};

class TypeParsingTest {

  /**
   * Parses a type from a given string
   *
   * @param  string $type
   * @param  lang.ast.Type
   */
  private function parse($type) {
    $language= Language::named('PHP');
    $parse= new Parse($language, new Tokens($type, static::class), null);
    $parse->forward();
    return $language->type($parse);
  }

  /** @return iterable */
  private function arrays() {
    yield ['array<string>', new IsLiteral('string')];
    yield ['array<Value>', new IsValue('Value')];
    yield ['array<array<int>>', new IsArray(new IsLiteral('int'))];
  }

  /** @return iterable */
  private function maps() {
    yield ['array<string, Value>', new IsLiteral('string'), new IsValue('Value')];
    yield ['array<string, array<int>>', new IsLiteral('string'), new IsArray(new IsLiteral('int'))];
    yield ['array<string, array<string, mixed>>', new IsLiteral('string'), new IsMap(new IsLiteral('string'), new IsLiteral('mixed'))];
  }

  /** @return iterable */
  private function generics() {
    yield ['Vector<string>', 'Vector', [new IsLiteral('string')]];
    yield ['Map<string, Value>', 'Map', [new IsLiteral('string'), new IsLiteral('Value')]];
    yield ['Map<string, Vector<int>>', 'Map', [new IsLiteral('string'), new IsGeneric('Vector', [new IsLiteral('int')])]];
  }

  /** @return iterable */
  private function functions() {
    yield ['function(): void', [], new IsLiteral('void')];
    yield ['function(string): string', [new IsLiteral('string')], new IsLiteral('string')];
    yield ['function(int, int): int', [new IsLiteral('int'), new IsLiteral('int')], new IsLiteral('int')];
  }

  /** @return iterable */
  private function unions() {
    yield ['int|string', [new IsLiteral('int'), new IsLiteral('string')]];
    yield ['string|File|URI', [new IsLiteral('string'), new IsValue('File'), new IsValue('URI')]];
  }

  /** @return iterable */
  private function intersections() {
    yield ['Countable&Traversable', [new IsValue('Countable'), new IsValue('Traversable')]];
    yield ['Countable&Traversable&Throwable', [new IsValue('Countable'), new IsValue('Traversable'), new IsValue('Throwable')]];
  }

  /** @return iterable */
  private function nullables() {
    yield ['?Countable', new IsValue('Countable')];
    yield ['?string|int', new IsUnion([new IsLiteral('string'), new IsLiteral('int')])];
    yield ['?Countable&Traversable', new IsIntersection([new IsValue('Countable'), new IsValue('Traversable')])];
  }

  #[Test, Values(['string', 'int', 'bool', 'float', 'void', 'never', 'array', 'object', 'callable', 'iterable', 'mixed'])]
  public function literal_type($declaration) {
    Assert::equals(new IsLiteral($declaration), $this->parse($declaration));
  }

  #[Test, Values(['Value', '\Error', '\lang\Value'])]
  public function value_type($declaration) {
    Assert::equals(new IsValue($declaration), $this->parse($declaration));
  }

  #[Test, Values(from: 'arrays')]
  public function array_type($declaration, $component) {
    Assert::equals(new IsArray($component), $this->parse($declaration));
  }

  #[Test, Values(from: 'maps')]
  public function map_type($declaration, $key, $value) {
    Assert::equals(new IsMap($key, $value), $this->parse($declaration));
  }

  #[Test, Values(from: 'generics')]
  public function generic_type($declaration, $base, $components) {
    Assert::equals(new IsGeneric($base, $components), $this->parse($declaration));
  }

  #[Test, Values(from: 'functions')]
  public function function_type($declaration, $arguments, $returns) {
    Assert::equals(new IsFunction($arguments, $returns), $this->parse($declaration));
  }

  #[Test, Values(from: 'unions')]
  public function union_type($declaration, $components) {
    Assert::equals(new IsUnion($components), $this->parse($declaration));
  }

  #[Test, Values(from: 'intersections')]
  public function intersection_type($declaration, $components) {
    Assert::equals(new IsIntersection($components), $this->parse($declaration));
  }

  #[Test, Values(from: 'nullables')]
  public function nullable_type($declaration, $type) {
    Assert::equals(new IsNullable($type), $this->parse($declaration));
  }

  #[Test]
  public function value_with_byref_param() {
    Assert::equals(new IsValue('Countable'), $this->parse('Countable &$param'));
  }

  #[Test]
  public function intersection_with_byref_param() {
    Assert::equals(
      new IsIntersection([new IsValue('Countable'), new IsValue('Traversable')]),
      $this->parse('Countable&Traversable &$param')
    );
  }
}
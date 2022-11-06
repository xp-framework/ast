<?php namespace lang\ast\unittest;

use lang\ast\types\{IsArray, IsGeneric, IsLiteral, IsMap, IsNullable, IsValue};
use lang\ast\{Language, Tokens, Type};
use unittest\{Assert, Test, Values};

class TypeTest {

  /**
   * Parse a type
   *
   * @param  string $t Type literal
   * @return lang.ast.Type
   */
  private function parse($t) {
    return Language::named('PHP')
      ->parse(new Tokens('function fixture(): '.$t.' { }'))
      ->tree()
      ->children()[0]
      ->signature
      ->returns
    ;
  }

  #[Test, Values(['string', 'int', 'bool', 'mixed', 'float', 'array', 'object', 'resource', 'iterable', 'callable', 'double', 'void', 'never', 'null', 'false', 'true'])]
  public function literals($t) {
    Assert::equals(new IsLiteral($t), $this->parse($t));
  }

  #[Test, Values(['int', 'string'])]
  public function arrays($t) {
    Assert::equals(new IsArray(new IsLiteral($t)), $this->parse('array<'.$t.'>'));
  }

  #[Test, Values(['int', 'string'])]
  public function maps($t) {
    Assert::equals(new IsMap(new IsLiteral('string'), new IsLiteral($t)), $this->parse('array<string, '.$t.'>'));
  }

  #[Test, Values(['int', 'string'])]
  public function nullable($t) {
    Assert::equals(new IsNullable(new IsLiteral($t)), $this->parse('?'.$t));
  }

  #[Test, Values(['int', 'string'])]
  public function generic_list($t) {
    Assert::equals(
      new IsGeneric(new IsValue('List'), [new IsLiteral($t)]),
      $this->parse('List<'.$t.'>')
    );
  }

  #[Test, Values(['int', 'string'])]
  public function generic_map($t) {
    Assert::equals(
      new IsGeneric(new IsValue('Map'), [new IsLiteral('string'), new IsLiteral($t)]),
      $this->parse('Map<string, '.$t.'>')
    );
  }

  #[Test]
  public function nested_generic() {
    Assert::equals(
      new IsGeneric(new IsValue('Filter'), [new IsGeneric(new IsValue('Set'), [new IsLiteral('T')])]),
      $this->parse('Filter<Set<T>>')
    );
  }

  #[Test]
  public function generic_nullable() {
    Assert::equals(
      new IsGeneric(new IsValue('Filter'), [new IsNullable(new IsLiteral('int'))]),
      $this->parse('Filter<?int>')
    );
  }

  #[Test]
  public function generic_wildcard() {
    Assert::equals(
      new IsGeneric(new IsValue('Filter'), [new IsLiteral('?')]),
      $this->parse('Filter<?>')
    );
  }

  #[Test]
  public function generic_wildcard_with_concrete_type() {
    Assert::equals(
      new IsGeneric(new IsValue('Filter'), [new IsLiteral('?'), new IsLiteral('int')]),
      $this->parse('Filter<?, int>')
    );
  }

  #[Test]
  public function generic_concrete_type_with_wildcard() {
    Assert::equals(
      new IsGeneric(new IsValue('Filter'), [new IsLiteral('int'), new IsLiteral('?')]),
      $this->parse('Filter<int, ?>')
    );
  }

  #[Test, Values(['self', 'static', 'parent', 'Value', '\\lang\\Value',])]
  public function values($t) {
    Assert::equals(new IsValue($t), $this->parse($t));
  }

  #[Test, Values(['string', '?string', 'mixed', 'array', 'array<int>', 'array<string, string>', 'Value', '\\lang\\Value', '?\\lang\\Value',])]
  public function literal($literal) {
    Assert::equals($literal, (new Type($literal))->literal());
  }

  #[Test, Values([['string', 'string'], ['?string', '?string'], ['mixed', 'var'], ['array', 'array'], ['array<int>', 'array<int>'], ['array<string, string>', 'array<string, string>'], ['Value', 'Value'], ['\\lang\\Value', 'lang.Value'], ['?\\lang\\Value', '?lang.Value'],])]
  public function name($literal, $name) {
    Assert::equals($name, (new Type($literal))->name());
  }
}
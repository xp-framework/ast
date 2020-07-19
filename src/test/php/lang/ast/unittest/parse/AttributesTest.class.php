<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{Annotated, Literal, ArrayLiteral};
use unittest\Assert;

class AttributesTest extends ParseTest {

  /**
   * Parses source into type
   *
   * @param  string $source
   * @return lang.ast.TypeDeclaration
   */
  private function type($source) {
    return $this->parse($source)->tree()->type('T');
  }

  /**
   * Assertion helper
   *
   * @param  var $expected
   * @param  lang.ast.Node $node
   * @throws unittest.AssertionFailedError
   * @return void
   */
  private function assertAnnotated($expected, $node) {
    Assert::equals($expected, cast($node, Annotated::class)->annotations);
  }

  #[@test]
  public function on_class() {
    $this->assertAnnotated(['service' => []], $this->type('@@service class T { }'));
  }

  #[@test]
  public function on_anonymous_class() {
    $this->assertAnnotated(
      ['service' => []],
      $this->parse('$object= new @@service class() { };')->tree()->children()[0]->expression->definition
    );
  }

  #[@test, @values([
  #  ['@@service function() { }', ['service' => []]],
  #  ['@@service fn() => true', ['service' => []]],
  #  ['@@service @@version(1) function() { }', ['service' => [], 'version' => [new Literal('1', self::LINE)]]],
  #  ['@@service @@version(1) fn() => true', ['service' => [], 'version' => [new Literal('1', self::LINE)]]],
  #])]
  public function on_function($declaration, $expected) {
    $this->assertAnnotated($expected, $this->parse('$apply= '.$declaration.';')->tree()->children()[0]->expression);
  }

  #[@test, @ignore('Not yet implemented')]
  public function on_constant() {
    $this->assertAnnotated(
      ['service' => []],
      $this->type('class T { @@test const FIXTURE = 1; }')->constant('FIXTURE')
    );
  }

  #[@test]
  public function on_property() {
    $this->assertAnnotated(
      ['test' => []], 
      $this->type('class T { @@test public $fixture; }')->property('fixture')
    );
  }

  #[@test]
  public function on_method() {
    $this->assertAnnotated(
      ['test' => []], 
      $this->type('class T { @@test public function fixture() { } }')->method('fixture')
    );
  }

  #[@test]
  public function on_parameter() {
    $this->assertAnnotated(
      ['test' => []], 
      $this->type('class T { public function fixture(@@test $p) { } }')->method('fixture')->signature->parameters[0]
    );
  }

  #[@test]
  public function without_value() {
    $this->assertAnnotated(['service' => []], $this->type('@@service() class T { }'));
  }

  #[@test, @values(['"test"', '1', '1.5', 'true', 'false', 'null'])]
  public function with_literal($value) {
    $this->assertAnnotated(
      ['service' => [new Literal($value, self::LINE)]],
      $this->type('@@service('.$value.') class T { }')
    );
  }

  #[@test]
  public function with_array() {
    $elements= [
      [null, new Literal('1', self::LINE)],
      [null, new Literal('2', self::LINE)],
    ];
    $this->assertAnnotated(
      ['service' => [new ArrayLiteral($elements, self::LINE)]],
      $this->type('@@service([1, 2]) class T { }')
    );
  }

  #[@test]
  public function with_map() {
    $elements= [
      [new Literal('"one"', self::LINE), new Literal('1', self::LINE)],
      [new Literal('"two"', self::LINE), new Literal('2', self::LINE)],
    ];
    $this->assertAnnotated(
      ['service' => [new ArrayLiteral($elements, self::LINE)]],
      $this->type('@@service(["one" => 1, "two" => 2]) class T { }')
    );
  }

  #[@test]
  public function two_annotations() {
    $this->assertAnnotated(
      ['Author' => [new Literal('"Test"', self::LINE)], 'Version' => [new Literal('2', self::LINE)]],
      $this->type('@@Author("Test") @@Version(2) class T { }')
    );
  }
}
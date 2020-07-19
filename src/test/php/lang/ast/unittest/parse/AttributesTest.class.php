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
   * Yield various attribute declarations and their expected values
   *
   * @return iterable
   */
  private function attributes() {
    yield ['@@service', ['service' => []]];
    yield ['@@service @@version(1)', ['service' => [], 'version' => [new Literal('1', self::LINE)]]];
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
  public function without_arguments() {
    $this->assertAnnotated(['service' => []], $this->type('@@service() class T { }'));
  }

  #[@test, @values(['"test"', '1', '1.5', 'true', 'false', 'null'])]
  public function with_literal_argument($value) {
    $this->assertAnnotated(
      ['service' => [new Literal($value, self::LINE)]],
      $this->type('@@service('.$value.') class T { }')
    );
  }

  #[@test]
  public function with_array_argument() {
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
  public function with_map_argument() {
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
  public function with_two_arguments() {
    $this->assertAnnotated(
      ['service' => [new Literal('1', self::LINE), new Literal('2', self::LINE)]],
      $this->type('@@service(1, 2) class T { }')
    );
  }

  #[@test]
  public function two_annotations() {
    $this->assertAnnotated(
      ['Author' => [new Literal('"Test"', self::LINE)], 'Version' => [new Literal('2', self::LINE)]],
      $this->type('@@Author("Test") @@Version(2) class T { }')
    );
  }

  #[@test, @values('attributes')]
  public function on_function($attributes, $expected) {
    $tree= $this->parse($attributes.' function fixture() { }')->tree();
    $this->assertAnnotated($expected, $tree->children()[0]);
  }

  #[@test, @values('attributes')]
  public function on_anonymous_class($attributes, $expected) {
    $tree= $this->parse('new '.$attributes.' class() { };')->tree();
    $this->assertAnnotated($expected, $tree->children()[0]->definition);
  }

  #[@test, @values('attributes')]
  public function on_closure($attributes, $expected) {
    $tree= $this->parse($attributes.' function() { };')->tree();
    $this->assertAnnotated($expected, $tree->children()[0]);
  }

  #[@test, @values('attributes')]
  public function on_lambda($attributes, $expected) {
    $tree= $this->parse($attributes.' fn() => true;')->tree();
    $this->assertAnnotated($expected, $tree->children()[0]);
  }

  #[@test, @values('attributes')]
  public function on_class($attributes, $expected) {
    $this->assertAnnotated($expected, $this->type($attributes.' class T { }'));
  }

  #[@test, @values('attributes')]
  public function on_trait($attributes, $expected) {
    $this->assertAnnotated($expected, $this->type($attributes.' trait T { }'));
  }

  #[@test, @values('attributes')]
  public function on_interface($attributes, $expected) {
    $this->assertAnnotated($expected, $this->type($attributes.' interface T { }'));
  }

  #[@test, @values('attributes')]
  public function on_constant($attributes, $expected) {
    $type= $this->type('class T { '.$attributes.' const FIXTURE = 1; }');
    $this->assertAnnotated($expected, $type->constant('FIXTURE'));
  }

  #[@test, @values('attributes')]
  public function on_property($attributes, $expected) {
    $type= $this->type('class T { '.$attributes.' public $fixture; }');
    $this->assertAnnotated($expected, $type->property('fixture'));
  }

  #[@test, @values('attributes')]
  public function on_method($attributes, $expected) {
    $type= $this->type('class T { '.$attributes.' public function fixture() { } }');
    $this->assertAnnotated($expected, $type->method('fixture'));
  }

  #[@test, @values('attributes')]
  public function on_parameter($attributes, $expected) {
    $type= $this->type('class T { public function fixture('.$attributes.' $p) { } }');
    $this->assertAnnotated($expected, $type->method('fixture')->signature->parameters[0]);
  }
}
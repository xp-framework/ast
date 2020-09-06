<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{Annotated, Literal, ArrayLiteral};
use unittest\Assert;

/** @see https://wiki.php.net/rfc/shorter_attribute_syntax_change */
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
    yield ['#[Service]', ['Service' => []]];
    yield ['#[Service, Version(1)]', ['Service' => [], 'Version' => [new Literal('1', self::LINE)]]];
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
    $this->assertAnnotated(
      ['Service' => []],
      $this->type('#[Service] class T { }')
    );
  }

  #[@test]
  public function with_empty_arguments() {
    $this->assertAnnotated(
      ['Service' => []],
      $this->type('#[Service()] class T { }')
    );
  }

  #[@test, @values(['"test"', '1', '1.5', 'true', 'false', 'null'])]
  public function with_literal_argument($value) {
    $this->assertAnnotated(
      ['Service' => [new Literal($value, self::LINE)]],
      $this->type('#[Service('.$value.')] class T { }')
    );
  }

  #[@test]
  public function with_array_argument() {
    $elements= [
      [null, new Literal('1', self::LINE)],
      [null, new Literal('2', self::LINE)],
    ];
    $this->assertAnnotated(
      ['Service' => [new ArrayLiteral($elements, self::LINE)]],
      $this->type('#[Service([1, 2])] class T { }')
    );
  }

  #[@test]
  public function with_map_argument() {
    $elements= [
      [new Literal('"one"', self::LINE), new Literal('1', self::LINE)],
      [new Literal('"two"', self::LINE), new Literal('2', self::LINE)],
    ];
    $this->assertAnnotated(
      ['Service' => [new ArrayLiteral($elements, self::LINE)]],
      $this->type('#[Service(["one" => 1, "two" => 2])] class T { }')
    );
  }

  #[@test]
  public function multiline() {
    $elements= [
      [new Literal('"one"', self::LINE + 2), new Literal('1', self::LINE + 2)],
      [new Literal('"two"', self::LINE + 3), new Literal('2', self::LINE + 3)],
    ];
    $this->assertAnnotated(['Values' => [new ArrayLiteral($elements, self::LINE + 1)]], $this->type('
      #[Values([
        "one" => 1,
        "two" => 2,
      ])]
      class T { }
    '));
  }

  #[@test]
  public function with_two_arguments() {
    $this->assertAnnotated(
      ['Service' => [new Literal('1', self::LINE), new Literal('2', self::LINE)]],
      $this->type('#[Service(1, 2)] class T { }')
    );
  }

  #[@test]
  public function two_annotations() {
    $this->assertAnnotated(
      ['Author' => [new Literal('"Test"', self::LINE)], 'Version' => [new Literal('2', self::LINE)]],
      $this->type('#[Author("Test"), Version(2)] class T { }')
    );
  }

  #[@test]
  public function name_resolved_to_namespace() {
    $this->assertAnnotated(
      ['example\\Test' => []],
      $this->parse('namespace example; #[Test] class T { }')->tree()->type('example\\T')
    );
  }

  #[@test]
  public function name_resolved_to_import() {
    $this->assertAnnotated(
      ['unittest\\Test' => []],
      $this->parse('namespace example; use unittest\Test; #[Test] class T { }')->tree()->type('example\\T')
    );
  }

  #[@test]
  public function name_resolved_to_import_alias() {
    $this->assertAnnotated(
      ['unittest\\TestAttribute' => []],
      $this->parse('use unittest\TestAttribute as Test; #[Test] class T { }')->tree()->type('T')
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
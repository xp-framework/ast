<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{Annotated, Literal, ArrayLiteral};
use unittest\Assert;

/** @see https://github.com/xp-framework/rfc/issues/16 */
class XpAnnotationsTest extends ParseTest {

  /**
   * Parses source into type
   *
   * @param  string $source
   * @return lang.ast.TypeDeclaration
   */
  private function type($source) {
    return $this->parse(trim($source))->tree()->type('T');
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
    $this->assertAnnotated(['service' => []], $this->type('
      #[@service] class T { }
    '));
  }

  #[@test, @values(['"test"', '1', '1.5', 'true', 'false', 'null'])]
  public function with_literal_argument($value) {
    $this->assertAnnotated(['service' => [new Literal($value, self::LINE)]], $this->type('
      #[@service('.$value.')]
      class T { }
    '));
  }

  #[@test]
  public function with_array_argument() {
    $elements= [
      [null, new Literal('1', self::LINE)],
      [null, new Literal('2', self::LINE)],
    ];
    $this->assertAnnotated(['service' => [new ArrayLiteral($elements, self::LINE)]], $this->type('
      #[@service([1, 2])]
      class T { }
    '));
  }

  #[@test]
  public function with_map_argument() {
    $elements= [
      [new Literal('"one"', self::LINE), new Literal('1', self::LINE)],
      [new Literal('"two"', self::LINE), new Literal('2', self::LINE)],
    ];
    $this->assertAnnotated(['service' => [new ArrayLiteral($elements, self::LINE)]], $this->type('
      #[@service(["one" => 1, "two" => 2])]
      class T { }'
    ));
  }

  #[@test]
  public function multiline() {
    $elements= [
      [new Literal('"one"', self::LINE + 1), new Literal('1', self::LINE + 1)],
      [new Literal('"two"', self::LINE + 2), new Literal('2', self::LINE + 2)],
    ];
    $this->assertAnnotated(['values' => [new ArrayLiteral($elements, self::LINE)]], $this->type('
      #[@values([
      #  "one" => 1,
      #  "two" => 2,
      #])]
      class T { }
    '));
  }

  #[@test]
  public function two_annotations() {
    $this->assertAnnotated(['service' => [], 'version' => [new Literal('2', self::LINE)]], $this->type('
      #[@service, @version(2)]
      class T { }
    '));
  }

  #[@test]
  public function on_class() {
    $this->assertAnnotated(['service' => []], $this->type('
      #[@service]
      class T { }
    '));
  }

  #[@test]
  public function on_trait() {
    $this->assertAnnotated(['service' => []], $this->type(
      '#[@service]
      trait T { }
    '));
  }

  #[@test]
  public function on_interface() {
    $this->assertAnnotated(['service' => []], $this->type(
      '#[@service]
      interface T { }
    '));
  }

  #[@test]
  public function on_property() {
    $type= $this->type('class T {
      #[@service]
      public $fixture;
    }');
    $this->assertAnnotated(['service' => []], $type->property('fixture'));
  }

  #[@test]
  public function on_method() {
    $type= $this->type('class T {
      #[@service]
      public function fixture() { }
    }');
    $this->assertAnnotated(['service' => []], $type->method('fixture'));
  }

  #[@test]
  public function on_parameter() {
    $type= $this->type('class T {
      #[@$arg: service]
      public function fixture($arg) { }
    }');
    $this->assertAnnotated(['service' => []], $type->method('fixture')->signature->parameters[0]);
  }
}
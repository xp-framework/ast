<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{Annotated, Literal, ArrayLiteral};
use unittest\Assert;

class AnnotationTest extends ParseTest {

  /**
   * Assertion helper
   *
   * @param  var $expected
   * @param  string $code
   * @throws unittest.AssertionFailedError
   * @return void
   */
  private function assertAnnotations($expected, $code) {
    $tree= $this->parse($code)->tree();
    Assert::equals($expected, cast($tree->children()[1], Annotated::class)->annotations);
  }

  #[@test]
  public function without_value() {
    $this->assertAnnotations(['service' => null], '<<service>> class T { }');
  }

  #[@test, @values(['"test"', '1', '1.5', 'true', 'false', 'null'])]
  public function with_literal($value) {
    $this->assertAnnotations(
      ['service' => new Literal($value, self::LINE)],
      '<<service('.$value.')>> class T { }'
    );
  }

  #[@test]
  public function with_array() {
    $elements= [
      [null, new Literal('1', self::LINE)],
      [null, new Literal('2', self::LINE)],
    ];
    $this->assertAnnotations(
      ['service' => new ArrayLiteral($elements, self::LINE)],
      '<<service([1, 2])>> class T { }'
    );
  }

  #[@test]
  public function with_map() {
    $elements= [
      [new Literal('"one"', self::LINE), new Literal('1', self::LINE)],
      [new Literal('"two"', self::LINE), new Literal('2', self::LINE)],
    ];
    $this->assertAnnotations(
      ['service' => new ArrayLiteral($elements, self::LINE)],
      '<<service(["one" => 1, "two" => 2])>> class T { }'
    );
  }

  #[@test]
  public function annotations_separated_by_commas() {
    $this->assertAnnotations(
      ['service' => null, 'path' => new Literal('"/"', self::LINE)],
      '<<service, path("/")>> class T { }'
    );
  }
}
<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{Annotated, Literal, ArrayLiteral};
use unittest\Assert;

class HackAnnotationsTest extends ParseTest {

  /**
   * Assertion helper
   *
   * @param  var $expected
   * @param  string $code
   * @throws unittest.AssertionFailedError
   * @return void
   */
  private function assertAnnotations($expected, $code) {
    $children= $this->parse($code)->tree()->children();
    Assert::equals($expected, cast($children[sizeof($children) - 1], Annotated::class)->annotations);
  }

  #[@test]
  public function without_value() {
    $this->assertAnnotations(['service' => []], '<<service>> class T { }');
  }

  #[@test, @values(['"test"', '1', '1.5', 'true', 'false', 'null'])]
  public function with_literal($value) {
    $this->assertAnnotations(
      ['service' => [new Literal($value, self::LINE)]],
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
      ['service' => [new ArrayLiteral($elements, self::LINE)]],
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
      ['service' => [new ArrayLiteral($elements, self::LINE)]],
      '<<service(["one" => 1, "two" => 2])>> class T { }'
    );
  }

  #[@test]
  public function annotations_separated_by_commas() {
    $this->assertAnnotations(
      ['service' => [], 'path' => [new Literal('"/"', self::LINE)]],
      '<<service, path("/")>> class T { }'
    );
  }

  #[@test]
  public function two_annotations() {
    $this->assertAnnotations(
      ['Author' => [new Literal('"Test"', self::LINE + 1)], 'Version' => [new Literal('2', self::LINE + 2)]], '
      <<Author("Test")>>
      <<Version(2)>>
      class T { }'
    );
  }
}
<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{Annotated, Literal, LambdaExpression};
use unittest\Assert;

/** @see https://github.com/xp-framework/rfc/issues/336 */
class ValuesTest extends ParseTest {

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
  private function assertValues($expected, $node) {
    Assert::equals($expected, cast($node, Annotated::class)->values);
  }

  #[@test]
  public function on_class() {
    $this->assertValues(
      ['using' => new Literal('"value"', self::LINE)],
      $this->type('#$using: "value" class T { }')
    );
  }

  #[@test]
  public function on_constant() {
    $this->assertValues(
      ['using' => new Literal('"value"', self::LINE)],
      $this->type('class T { #$using: "value" const FIXTURE = "test"; }')->constant('FIXTURE')
    );
  }

  #[@test]
  public function on_property() {
    $this->assertValues(
      ['using' => new Literal('"value"', self::LINE)],
      $this->type('class T { #$using: "value" public $fixture; }')->property('fixture')
    );
  }

  #[@test]
  public function on_method() {
    $this->assertValues(
      ['using' => new Literal('"value"', self::LINE)],
      $this->type('class T { #$using: "value" public function fixture() { } }')->method('fixture')
    );
  }

  #[@test]
  public function values_on_multiple_lines() {
    $this->assertValues(
      ['author' => new Literal('"test"', self::LINE + 1), 'version'  => new Literal('1', self::LINE + 2)],
      $this->type('
        #$author: "test"
        #$version: 1
        class T { }
      ')
    );
  }

  #[@test]
  public function with_function() {
    $type= $this->type('
      #$using: fn() => version_compare(PHP_VERSION, "7.0.0", ">=")
      class T { }
    ');
    Assert::instance(LambdaExpression::class, cast($type, Annotated::class)->values['using']);
  }
}
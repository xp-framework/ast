<?php namespace lang\ast\unittest\parse;

use lang\ast\Errors;
use unittest\{Assert, Test};

class ErrorsTest extends ParseTest {

  /**
   * Assertion helper
   *
   * @param  string $message
   * @param  iterable $parse
   * @throws unittest.AssertionFailedError
   */
  private function assertError($message, $parse) {
    try {
      $parse->tree();
      $this->fail('No exception raised', null, Errors::class);
    } catch (Errors $expected) {
      Assert::equals($message, $expected->getMessage());
    }
  }

  #[Test]
  public function missing_semicolon() {
    $this->assertError(
      'Missing semicolon after assignment statement',
      $this->parse('$a= 1 $b= 1;')
    );
  }

  #[Test]
  public function unclosed_brace_in_arguments() {
    $this->assertError(
      'Expected ") or ,", have "(end)" in argument list',
      $this->parse('call(')
    );
  }

  #[Test]
  public function unclosed_brace_in_parameters() {
    $this->assertError(
      'Expected ",", have "(end)" in parameter list',
      $this->parse('function($a')
    );
  }

  #[Test]
  public function unclosed_type() {
    $this->assertError(
      'Expected a type, modifier, property, annotation, method or "}", have "-"',
      $this->parse('class T { - }')
    );
  }

  #[Test]
  public function missing_comma_in_implements() {
    $this->assertError(
      'Expected ", or {", have "B" in interfaces list',
      $this->parse('class A implements I B { }')
    );
  }

  #[Test]
  public function missing_comma_in_interface_parents() {
    $this->assertError(
      'Expected ", or {", have "B" in interface parents',
      $this->parse('interface I extends A B { }')
    );
  }

  #[Test]
  public function unclosed_annotation() {
    $this->assertError(
      'Expected ", or ]", have "(end)" in attributes',
      $this->parse('#[Annotation')
    );
  }

  #[Test]
  public function unclosed_offset() {
    $this->assertError(
      'Expected "]", have ";" in offset access',
      $this->parse('$a[$s[0]= 5;')
    );
  }
}
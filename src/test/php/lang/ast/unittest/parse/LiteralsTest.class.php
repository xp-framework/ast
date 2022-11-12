<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{ArrayLiteral, Literal, Variable};
use unittest\{Assert, Test, Values};

class LiteralsTest extends ParseTest {

  #[Test, Values(['0', '1'])]
  public function integer($input) {
    $this->assertParsed([new Literal($input, self::LINE)], $input.';');
  }

  #[Test, Values(['0x00', '0x01', '0xFF', '0xff'])]
  public function hexadecimal($input) {
    $this->assertParsed([new Literal($input, self::LINE)], $input.';');
  }

  #[Test, Values(['00', '01', '010', '0777', '0o16', '0O16'])]
  public function octal($input) {
    $this->assertParsed([new Literal($input, self::LINE)], $input.';');
  }

  #[Test, Values(['1.0', '1.5'])]
  public function decimal($input) {
    $this->assertParsed([new Literal($input, self::LINE)], $input.';');
  }

  #[Test]
  public function bool_true() {
    $this->assertParsed([new Literal('true', self::LINE)], 'true;');
  }

  #[Test]
  public function bool_false() {
    $this->assertParsed([new Literal('false', self::LINE)], 'false;');
  }

  #[Test]
  public function null() {
    $this->assertParsed([new Literal('null', self::LINE)], 'null;');
  }

  #[Test]
  public function empty_string() {
    $this->assertParsed([new Literal('""', self::LINE)], '"";');
  }

  #[Test]
  public function non_empty_string() {
    $this->assertParsed([new Literal('"Test"', self::LINE)], '"Test";');
  }

  #[Test]
  public function empty_array() {
    $this->assertParsed([new ArrayLiteral([], self::LINE)], '[];');
  }

  #[Test]
  public function int_array() {
    $pairs= [
      [null, new Literal('1', self::LINE)],
      [null, new Literal('2', self::LINE)]
    ];
    $this->assertParsed([new ArrayLiteral($pairs, self::LINE)], '[1, 2];');
  }

  #[Test]
  public function key_value_map() {
    $pair= [new Literal('"key"', self::LINE), new Literal('"value"', self::LINE)];
    $this->assertParsed([new ArrayLiteral([$pair], self::LINE)], '["key" => "value"];');
  }

  #[Test]
  public function dangling_comma_in_array() {
    $pair= [null, new Literal('1', self::LINE)];
    $this->assertParsed([new ArrayLiteral([$pair], self::LINE)], '[1, ];');
  }

  #[Test]
  public function dangling_comma_in_key_value_map() {
    $pair= [new Literal('"key"', self::LINE), new Literal('"value"', self::LINE)];
    $this->assertParsed([new ArrayLiteral([$pair], self::LINE)], '["key" => "value", ];');
  }

  #[Test]
  public function empty_element_at_start_in_short_list() {
    $a= [null, new Variable('a', self::LINE)];
    $this->assertParsed([new ArrayLiteral([[null, null], $a], self::LINE)], '[, $a];');
  }

  #[Test]
  public function empty_element_at_end_in_short_list() {
    $a= [null, new Variable('a', self::LINE)];
    $this->assertParsed([new ArrayLiteral([$a, [null, null]], self::LINE)], '[$a, , ];');
  }

  #[Test]
  public function empty_element_between_in_short_list() {
    $a= [null, new Variable('a', self::LINE)];
    $b= [null, new Variable('b', self::LINE)];
    $this->assertParsed([new ArrayLiteral([$a, [null, null], $b], self::LINE)], '[$a, , $b];');
  }
}
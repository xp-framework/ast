<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{ArrayLiteral, Literal};
use test\{Assert, Test, Values};

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
  public function exec_statement() {
    $this->assertParsed([new Literal('`ls -al`', self::LINE)], '`ls -al`;');
  }

  #[Test, Values(['[];', ['array();']])]
  public function empty_array($declaration) {
    $this->assertParsed([new ArrayLiteral([], self::LINE)], $declaration);
  }

  #[Test, Values(['[1, 2];', ['array(1, 2);']])]
  public function int_array($declaration) {
    $pairs= [
      [null, new Literal('1', self::LINE)],
      [null, new Literal('2', self::LINE)]
    ];
    $this->assertParsed([new ArrayLiteral($pairs, self::LINE)], $declaration);
  }

  #[Test, Values(['["key" => "value"];', ['array("key" => "value");']])]
  public function key_value_map($declaration) {
    $pair= [new Literal('"key"', self::LINE), new Literal('"value"', self::LINE)];
    $this->assertParsed([new ArrayLiteral([$pair], self::LINE)], $declaration);
  }

  #[Test, Values(['[1, ];', 'array(1, );'])]
  public function dangling_comma_in_array($declaration) {
    $pair= [null, new Literal('1', self::LINE)];
    $this->assertParsed([new ArrayLiteral([$pair], self::LINE)], $declaration);
  }

  #[Test, Values(['["key" => "value", ];', 'array("key" => "value", );'])]
  public function dangling_comma_in_key_value_map($declaration) {
    $pair= [new Literal('"key"', self::LINE), new Literal('"value"', self::LINE)];
    $this->assertParsed([new ArrayLiteral([$pair], self::LINE)], $declaration);
  }

  #[Test, Values(['EOD', '"EOD"', "'EOD'"])]
  public function heredoc($label) {
    $nowdoc= (
      "<<<{$label}\n".
      "Line 1\n".
      "Line 2\n".
      "\n".
      "Line 4\n".
      "EOD"
    );
    $this->assertParsed([new Literal($nowdoc, self::LINE + 5)], $nowdoc.';');
  }

  #[Test]
  public function heredoc_indentation() {
    $nowdoc= (
      "<<<EOD\n".
      "  Line 1\n".
      "  Line 2\n".
      "\n".
      "  Line 4\n".
      "  EOD"
    );
    $this->assertParsed([new Literal($nowdoc, self::LINE + 5)], $nowdoc.';');
  }
}
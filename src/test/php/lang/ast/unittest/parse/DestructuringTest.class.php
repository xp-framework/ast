<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{ArrayLiteral, Literal, Variable};
use unittest\{Assert, Test, Values};

class DestructuringTest extends ParseTest {

  #[Test, Values(['[, $a];', 'list(, $a);'])]
  public function empty_element_at_start_in_short_list($input) {
    $a= [null, new Variable('a', self::LINE)];
    $this->assertParsed([new ArrayLiteral([[null, null], $a], self::LINE)], $input);
  }

  #[Test, Values(['[$a, ,];', 'list($a, ,);'])]
  public function empty_element_at_end_in_short_list($input) {
    $a= [null, new Variable('a', self::LINE)];
    $this->assertParsed([new ArrayLiteral([$a, [null, null]], self::LINE)], $input);
  }

  #[Test, Values(['[$a, , $b];', 'list($a, , $b);'])]
  public function empty_element_between_in_short_list($input) {
    $a= [null, new Variable('a', self::LINE)];
    $b= [null, new Variable('b', self::LINE)];
    $this->assertParsed([new ArrayLiteral([$a, [null, null], $b], self::LINE)], $input);
  }

  #[Test, Values(['[$a,];', 'list($a,);'])]
  public function trailing_comma($input) {
    $a= [null, new Variable('a', self::LINE)];
    $this->assertParsed([new ArrayLiteral([$a], self::LINE)], $input);
  }
}
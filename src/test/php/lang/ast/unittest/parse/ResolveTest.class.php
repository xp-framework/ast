<?php namespace lang\ast\unittest\parse;

use lang\IllegalArgumentException;
use lang\ast\ParseTree;
use unittest\Assert;

class ResolveTest extends ParseTest {

  /**
   * Fetches a member by a give name
   *
   * @param  string $type Type declaration
   * @param  string $name Member name
   * @return lang.ast.nodes.Member
   * @throws lang.IllegalArgumentException
   */
  private function member($type, $name) {
    foreach ((new ParseTree($this->parse($type)))->query('/*/@member') as $member) {
      if ($name === $member->name) return $member;
    }
    throw new IllegalArgumentException('No such member "'.$name.'"');
  }

  #[@test, @values([
  #  ['public $m= 1;', 1],
  #  ['public $m= 6.1;', 6.1],
  #  ['public $m= null;', null],
  #  ['public $m= false;', false],
  #  ['public $m= true;', true],
  #  ['public $m= "test";', 'test'],
  #])]
  public function scalars($declaration, $expected) {
    Assert::equals($expected, $this->member('class T { '.$declaration.' }', 'm')->expression->resolve());
  }

  #[@test, @values([
  #  ['public $m= [];', []],
  #  ['public $m= [1, 2, 3];', [1, 2, 3]],
  #  ['public $m= ["key" => "value"];', ['key' => 'value']],
  #  ['public $m= [1, "color" => "green"];', [1, 'color' => 'green']],
  #])]
  public function arrays($declaration, $expected) {
    Assert::equals($expected, $this->member('class T { '.$declaration.' }', 'm')->expression->resolve());
  }

  #[@test, @values([
  #  ['public $m= 1 ? 2 : 1;', 2],
  #  ['public $m= 0 ? 2 : 1;', 1],
  #])]
  public function ternary_expr($declaration, $expected) {
    Assert::equals($expected, $this->member('class T { '.$declaration.' }', 'm')->expression->resolve());
  }

  #[@test, @values([
  #  ['public $m= ~1;', -2],
  #  ['public $m= +1;', 1],
  #  ['public $m= -1;', -1],
  #  ['public $m= !true;', false],
  #])]
  public function unary_expr($declaration, $expected) {
    Assert::equals($expected, $this->member('class T { '.$declaration.' }', 'm')->expression->resolve());
  }

  #[@test, @values([
  #  ['public $m= 1 ?: 2;', 1],
  #  ['public $m= 1 ?? 2;', 1],
  #  ['public $m= 2 - 1;', 1],
  #  ['public $m= 1 + 1;', 2],
  #  ['public $m= 1 << 2;', 4],
  #  ['public $m= 1 >> 2;', 0],
  #  ['public $m= 2 ** 4;', 16],
  #  ['public $m= 2 ** 4;', 16],
  #  ['public $m= 9 % 2;', 1],
  #  ['public $m= 9 | 1;', 9],
  #  ['public $m= 9 ^ 1;', 8],
  #  ['public $m= "test"."ed";', 'tested'],
  #  ['public $m= true || false;', true],
  #  ['public $m= true && false;', false],
  #])]
  public function binary_expr($declaration, $expected) {
    Assert::equals($expected, $this->member('class T { '.$declaration.' }', 'm')->expression->resolve());
  }

  #[@test, @ignore('Not yet implemented'), @values([
  #  ['public $m= T::class;', 'T'],
  #  ['public $m= self::class;', 'T'],
  #  ['public $m= parent::INHERITED;', '1'],
  #  ['const DECLARED = 2; public $m= self::DECLARED;', '2'],
  #])]
  public function constant_reference($declaration, $expected) {
    Assert::equals(
      $expected,
      $this->member('class B { const INHERITED = 1; } class T extends B { '.$declaration.' }', 'm')->expression->resolve()
    );
  }
}
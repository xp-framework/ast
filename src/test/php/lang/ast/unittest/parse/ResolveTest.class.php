<?php namespace lang\ast\unittest\parse;

use lang\ast\Scope;
use lang\{Type, IllegalArgumentException, DynamicClassLoader};
use unittest\Assert;

class ResolveTest extends ParseTest {
  private $scope;

  #[@before]
  public function scope() {
    $this->scope= new Scope();
  }

  /**
   * Fetches a property by a given name
   *
   * @param  string $type Type declaration
   * @param  string $name Property name
   * @return lang.ast.nodes.Property
   * @throws lang.IllegalArgumentException
   */
  private function property($type, $name) {
    $p= $this->parse($type, $this->scope)->tree()->scope()->type('T')->property($name);
    if (null === $p) {
      throw new IllegalArgumentException('No such property "'.$name.'"');
    }

    return $p;
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
    Assert::equals($expected, $this->property('class T { '.$declaration.' }', 'm')->resolve($this->scope));
  }

  #[@test, @values([
  #  ['public $m= [];', []],
  #  ['public $m= [1, 2, 3];', [1, 2, 3]],
  #  ['public $m= ["key" => "value"];', ['key' => 'value']],
  #  ['public $m= [1, "color" => "green"];', [1, 'color' => 'green']],
  #])]
  public function arrays($declaration, $expected) {
    Assert::equals($expected, $this->property('class T { '.$declaration.' }', 'm')->resolve($this->scope));
  }

  #[@test, @values([
  #  ['public $m= 1 ? 2 : 1;', 2],
  #  ['public $m= 0 ? 2 : 1;', 1],
  #])]
  public function ternary_expr($declaration, $expected) {
    Assert::equals($expected, $this->property('class T { '.$declaration.' }', 'm')->resolve($this->scope));
  }

  #[@test, @values([
  #  ['public $m= ~1;', -2],
  #  ['public $m= +1;', 1],
  #  ['public $m= -1;', -1],
  #  ['public $m= !true;', false],
  #])]
  public function unary_expr($declaration, $expected) {
    Assert::equals($expected, $this->property('class T { '.$declaration.' }', 'm')->resolve($this->scope));
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
    Assert::equals($expected, $this->property('class T { '.$declaration.' }', 'm')->resolve($this->scope));
  }

  #[@test, @values([
  #  ['public $m= T::class;', 'T'],
  #  ['public $m= self::class;', 'T'],
  #  ['public $m= parent::class;', 'Base'],
  #  ['public $m= parent::INHERITED;', 1],
  #  ['public $m= self::INHERITED;', 1],
  #  ['public $m= self::$INCLUDED;', true],
  #  ['public $m= self::IMPLEMENTED;', 'impl'],
  #  ['public $m= Type::$VOID;', Type::$VOID],
  #  ['public $m= DynamicClassLoader::DEVICE;', DynamicClassLoader::DEVICE],
  #  ['public static $DECLARED = 2; public $m= self::$DECLARED;', 2],
  #  ['const DECLARED = 2; public $m= self::DECLARED;', 2],
  #])]
  public function member_reference($declaration, $expected) {
    $declaration= sprintf('
      use lang\{Type, DynamicClassLoader};

      class Base { const INHERITED= 1; }
      trait Part { public static $INCLUDED= true; }
      interface Impl { const IMPLEMENTED= "impl"; }
      class T extends Base implements Impl { use Part; %s }',
      $declaration
    );
    Assert::equals($expected, $this->property($declaration, 'm')->resolve($this->scope));
  }
}
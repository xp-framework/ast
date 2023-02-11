<?php namespace lang\ast\unittest\parse;

use lang\ast\Errors;
use lang\ast\nodes\{
  ClassDeclaration,
  InterfaceDeclaration,
  EnumDeclaration,
  EnumCase,
  NamespaceDeclaration,
  TraitDeclaration,
  UseExpression,
  Literal
};
use lang\ast\types\IsValue;
use test\{Assert, Expect, Test};

class TypesTest extends ParseTest {

  #[Test]
  public function empty_class() {
    $this->assertParsed(
      [new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE)],
      'class A { }'
    );
  }

  #[Test]
  public function class_with_parent() {
    $this->assertParsed(
      [new ClassDeclaration([], new IsValue('\\A'), new IsValue('\\B'), [], [], null, null, self::LINE)],
      'class A extends B { }'
    );
  }

  #[Test]
  public function class_with_interface() {
    $this->assertParsed(
      [new ClassDeclaration([], new IsValue('\\A'), null, [new IsValue('\\C')], [], null, null, self::LINE)],
      'class A implements C { }'
    );
  }

  #[Test]
  public function class_with_interfaces() {
    $this->assertParsed(
      [new ClassDeclaration([], new IsValue('\\A'), null, [new IsValue('\\C'), new IsValue('\\D')], [], null, null, self::LINE)],
      'class A implements C, D { }'
    );
  }

  #[Test]
  public function abstract_class() {
    $this->assertParsed(
      [new ClassDeclaration(['abstract'], new IsValue('\\A'), null, [], [], null, null, self::LINE)],
      'abstract class A { }'
    );
  }

  #[Test]
  public function final_class() {
    $this->assertParsed(
      [new ClassDeclaration(['final'], new IsValue('\\A'), null, [], [], null, null, self::LINE)],
      'final class A { }'
    );
  }

  #[Test]
  public function empty_interface() {
    $this->assertParsed(
      [new InterfaceDeclaration([], new IsValue('\\A'), [], [], null, null, self::LINE)],
      'interface A { }'
    );
  }

  #[Test]
  public function interface_with_parent() {
    $this->assertParsed(
      [new InterfaceDeclaration([], new IsValue('\\A'), [new IsValue('\\B')], [], null, null, self::LINE)],
      'interface A extends B { }'
    );
  }

  #[Test]
  public function interface_with_parents() {
    $this->assertParsed(
      [new InterfaceDeclaration([], new IsValue('\\A'), [new IsValue('\\B'), new IsValue('\\C')], [], null, null, self::LINE)],
      'interface A extends B, C { }'
    );
  }

  #[Test]
  public function empty_trait() {
    $this->assertParsed(
      [new TraitDeclaration([], new IsValue('\\A'), [], null, null, self::LINE)],
      'trait A { }'
    );
  }

  #[Test]
  public function empty_unit_enum() {
    $this->assertParsed(
      [new EnumDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE)],
      'enum A { }'
    );
  }

  #[Test]
  public function empty_backed_enum() {
    $this->assertParsed(
      [new EnumDeclaration([], new IsValue('\\A'), 'string', [], [], null, null, self::LINE)],
      'enum A: string { }'
    );
  }

  #[Test]
  public function unit_enum_with_cases() {
    $enum= new EnumDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $enum->declare(new EnumCase('ONE', null, null, null, self::LINE));
    $enum->declare(new EnumCase('TWO', null, null, null, self::LINE));
    $this->assertParsed([$enum], 'enum A { case ONE; case TWO; }');
  }

  #[Test]
  public function backed_enum_with_cases() {
    $enum= new EnumDeclaration([], new IsValue('\\A'), 'int', [], [], null, null, self::LINE);
    $enum->declare(new EnumCase('ONE', new Literal('1', self::LINE), null, null, self::LINE));
    $enum->declare(new EnumCase('TWO', new Literal('2', self::LINE), null, null, self::LINE));
    $this->assertParsed([$enum], 'enum A: int { case ONE = 1; case TWO = 2; }');
  }

  #[Test]
  public function unit_enum_with_grouped_cases() {
    $enum= new EnumDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $enum->declare(new EnumCase('ONE', null, null, null, self::LINE));
    $enum->declare(new EnumCase('TWO', null, null, null, self::LINE));
    $this->assertParsed([$enum], 'enum A { case ONE, TWO; }');
  }

  #[Test]
  public function class_with_trait() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->body[]= new UseExpression(['\\B'], [], self::LINE);
    $this->assertParsed([$class], 'class A { use B; }');
  }

  #[Test]
  public function class_with_multiple_traits() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->body[]= new UseExpression(['\\B'], [], self::LINE);
    $class->body[]= new UseExpression(['\\C'], [], self::LINE);
    $this->assertParsed([$class], 'class A { use B; use C; }');
  }

  #[Test]
  public function class_with_comma_separated_traits() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->body[]= new UseExpression(['\\B', '\\C'], [], self::LINE);
    $this->assertParsed([$class], 'class A { use B, C; }');
  }

  #[Test]
  public function class_with_trait_and_aliases() {
    $aliases= ['a' => ['as' => 'first'], '\\B::b' => ['as' => 'second'], '\\C::c' => ['insteadof' => '\\B']];
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->body[]= new UseExpression(['\\B', '\\C'], $aliases, self::LINE + 1);

    $this->assertParsed([$class], 'class A {
      use B, C {
        a as first;
        B::b as second;
        C::c insteadof B;
      }
    }');
  }

  #[Test]
  public function class_in_namespace() {
    $this->assertParsed(
      [new NamespaceDeclaration('test', self::LINE), new ClassDeclaration([], new IsValue('\\test\\A'), null, [], [], null, null, self::LINE)],
      'namespace test; class A { }'
    );
  }

  #[Test, Expect(class: Errors::class, message: '/Cannot redeclare method b\(\)/')]
  public function cannot_redeclare_method() {
    $this->parse('class A { public function b() { } public function b() { }}')->tree();
  }

  #[Test, Expect(class: Errors::class, message: '/Cannot redeclare property \$b/')]
  public function cannot_redeclare_property() {
    $this->parse('class A { public $b; private $b; }')->tree();
  }

  #[Test, Expect(class: Errors::class, message: '/Cannot redeclare constant B/')]
  public function cannot_redeclare_constant() {
    $this->parse('class A { const B = 1; const B = 3; }')->tree();
  }
}
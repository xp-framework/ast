<?php namespace lang\ast\unittest\parse;

use lang\ast\Errors;
use lang\ast\nodes\{ClassDeclaration, InterfaceDeclaration, NamespaceDeclaration, TraitDeclaration, UseExpression};
use unittest\{Assert, Expect, Test};

class TypesTest extends ParseTest {

  #[Test]
  public function empty_class() {
    $this->assertParsed(
      [new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE)],
      'class A { }'
    );
  }

  #[Test]
  public function class_with_parent() {
    $this->assertParsed(
      [new ClassDeclaration([], '\\A', '\\B', [], [], [], null, self::LINE)],
      'class A extends B { }'
    );
  }

  #[Test]
  public function class_with_interface() {
    $this->assertParsed(
      [new ClassDeclaration([], '\\A', null, ['\\C'], [], [], null, self::LINE)],
      'class A implements C { }'
    );
  }

  #[Test]
  public function class_with_interfaces() {
    $this->assertParsed(
      [new ClassDeclaration([], '\\A', null, ['\\C', '\\D'], [], [], null, self::LINE)],
      'class A implements C, D { }'
    );
  }

  #[Test]
  public function abstract_class() {
    $this->assertParsed(
      [new ClassDeclaration(['abstract'], '\\A', null, [], [], [], null, self::LINE)],
      'abstract class A { }'
    );
  }

  #[Test]
  public function final_class() {
    $this->assertParsed(
      [new ClassDeclaration(['final'], '\\A', null, [], [], [], null, self::LINE)],
      'final class A { }'
    );
  }

  #[Test]
  public function empty_interface() {
    $this->assertParsed(
      [new InterfaceDeclaration([], '\\A', [], [], [], null, self::LINE)],
      'interface A { }'
    );
  }

  #[Test]
  public function interface_with_parent() {
    $this->assertParsed(
      [new InterfaceDeclaration([], '\\A', ['\\B'], [], [], null, self::LINE)],
      'interface A extends B { }'
    );
  }

  #[Test]
  public function interface_with_parents() {
    $this->assertParsed(
      [new InterfaceDeclaration([], '\\A', ['\\B', '\\C'], [], [], null, self::LINE)],
      'interface A extends B, C { }'
    );
  }

  #[Test]
  public function empty_trait() {
    $this->assertParsed(
      [new TraitDeclaration([], '\\A', [], [], null, self::LINE)],
      'trait A { }'
    );
  }

  #[Test]
  public function class_with_trait() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->body[]= new UseExpression(['\\B'], [], self::LINE);
    $this->assertParsed([$class], 'class A { use B; }');
  }

  #[Test]
  public function class_with_multiple_traits() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->body[]= new UseExpression(['\\B'], [], self::LINE);
    $class->body[]= new UseExpression(['\\C'], [], self::LINE);
    $this->assertParsed([$class], 'class A { use B; use C; }');
  }

  #[Test]
  public function class_with_comma_separated_traits() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->body[]= new UseExpression(['\\B', '\\C'], [], self::LINE);
    $this->assertParsed([$class], 'class A { use B, C; }');
  }

  #[Test]
  public function class_with_trait_and_aliases() {
    $aliases= ['a' => ['as' => 'first'], '\\B::b' => ['as' => 'second'], '\\C::c' => ['insteadof' => '\\B']];
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
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
      [new NamespaceDeclaration('test', self::LINE), new ClassDeclaration([], '\\test\\A', null, [], [], [], null, self::LINE)],
      'namespace test; class A { }'
    );
  }

  #[Test, Expect(['class' => Errors::class, 'withMessage' => 'Cannot redeclare method b()'])]
  public function cannot_redeclare_method() {
    $this->parse('class A { public function b() { } public function b() { }}')->tree();
  }

  #[Test, Expect(['class' => Errors::class, 'withMessage' => 'Cannot redeclare property $b'])]
  public function cannot_redeclare_property() {
    $this->parse('class A { public $b; private $b; }')->tree();
  }

  #[Test, Expect(['class' => Errors::class, 'withMessage' => 'Cannot redeclare constant B'])]
  public function cannot_redeclare_constant() {
    $this->parse('class A { const B = 1; const B = 3; }')->tree();
  }
}
<?php namespace lang\ast\unittest\parse;

use lang\ast\Type;
use lang\ast\nodes\{ClassDeclaration, Constant, InstanceExpression, InvokeExpression, Literal, Method, Property, ScopeExpression, Signature, Variable};
use unittest\Assert;

class MembersTest extends ParseTest {

  #[@test]
  public function private_instance_property() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Property(['private'], 'a', null, null, [], null, self::LINE));

    $this->assertParsed([$class], 'class A { private $a; }');
  }

  #[@test]
  public function private_instance_properties() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Property(['private'], 'a', null, null, [], null, self::LINE));
    $class->declare(new Property(['private'], 'b', null, null, [], null, self::LINE));

    $this->assertParsed([$class], 'class A { private $a, $b; }');
  }

  #[@test]
  public function private_instance_method() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Method(['private'], 'a', new Signature([], null), [], [], null, self::LINE));

    $this->assertParsed([$class], 'class A { private function a() { } }');
  }

  #[@test]
  public function private_static_method() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Method(['private', 'static'], 'a', new Signature([], null), [], [], null, self::LINE));

    $this->assertParsed([$class], 'class A { private static function a() { } }');
  }

  #[@test]
  public function class_constant() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Constant([], 'T', null, new Literal('1', self::LINE), self::LINE));

    $this->assertParsed([$class], 'class A { const T = 1; }');
  }

  #[@test]
  public function class_constants() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Constant([], 'T', null, new Literal('1', self::LINE), self::LINE));
    $class->declare(new Constant([], 'S', null, new Literal('2', self::LINE), self::LINE));

    $this->assertParsed([$class], 'class A { const T = 1, S = 2; }');
  }

  #[@test]
  public function private_class_constant() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Constant(['private'], 'T', null, new Literal('1', self::LINE), self::LINE));

    $this->assertParsed([$class], 'class A { private const T = 1; }');
  }

  #[@test]
  public function method_with_return_type() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Method(['public'], 'a', new Signature([], new Type('void')), [], [], null, self::LINE));

    $this->assertParsed([$class], 'class A { public function a(): void { } }');
  }

  #[@test]
  public function method_with_annotation() {
    $annotations= ['test' => null];
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Method(['public'], 'a', new Signature([], null), [], $annotations, null, self::LINE));

    $this->assertParsed([$class], 'class A { <<test>> public function a() { } }');
  }

  #[@test]
  public function method_with_annotations() {
    $annotations= ['test' => null, 'ignore' => new Literal('"Not implemented"', self::LINE)];
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Method(['public'], 'a', new Signature([], null), [], $annotations, null, self::LINE));

    $this->assertParsed([$class], 'class A { <<test, ignore("Not implemented")>> public function a() { } }');
  }

  #[@test]
  public function instance_property_access() {
    $this->assertParsed(
      [new InstanceExpression(new Variable('a', self::LINE), new Literal('member', self::LINE), self::LINE)],
      '$a->member;'
    );
  }

  #[@test]
  public function dynamic_instance_property_access_via_variable() {
    $this->assertParsed(
      [new InstanceExpression(new Variable('a', self::LINE), new Variable('member', self::LINE), self::LINE)],
      '$a->{$member};'
    );
  }

  #[@test]
  public function dynamic_instance_property_access_via_expression() {
    $member= new InvokeExpression(
      new InstanceExpression(new Variable('field', self::LINE), new Literal('get', self::LINE), self::LINE),
      [new Variable('instance', self::LINE)],
      self::LINE
    );
    $this->assertParsed(
      [new InstanceExpression(new Variable('a', self::LINE), $member, self::LINE)],
      '$a->{$field->get($instance)};'
    );
  }

  #[@test]
  public function static_property_access() {
    $this->assertParsed(
      [new ScopeExpression('\\A', new Variable('member', self::LINE), self::LINE)],
      'A::$member;'
    );
  }

  #[@test, @values(['self', 'parent', 'static'])]
  public function scope_resolution($scope) {
    $this->assertParsed(
      [new ScopeExpression($scope, new Literal('class', self::LINE), self::LINE)],
      $scope.'::class;'
    );
  }

  #[@test]
  public function class_resolution() {
    $this->assertParsed(
      [new ScopeExpression('\\A', new Literal('class', self::LINE), self::LINE)],
      'A::class;'
    );
  }

  #[@test]
  public function instance_method_invocation() {
    $this->assertParsed(
      [new InvokeExpression(
        new InstanceExpression(new Variable('a', self::LINE), new Literal('member', self::LINE), self::LINE),
        [new Literal('1', self::LINE)],
        self::LINE
      )],
      '$a->member(1);'
    );
  }

  #[@test]
  public function static_method_invocation() {
    $this->assertParsed(
      [new ScopeExpression(
        '\\A',
        new InvokeExpression(new Literal('member', self::LINE), [new Literal('1', self::LINE)], self::LINE),
        self::LINE
      )],
      'A::member(1);'
    );
  }

  #[@test]
  public function typed_property() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Property(['private'], 'a', new Type('string'), null, [], null, self::LINE));

    $this->assertParsed([$class], 'class A { private string $a; }');
  }

  #[@test]
  public function typed_property_with_value() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Property(['private'], 'a', new Type('string'), new Literal('"test"', self::LINE), [], null, self::LINE));

    $this->assertParsed([$class], 'class A { private string $a = "test"; }');
  }

  #[@test]
  public function typed_properties() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Property(['private'], 'a', new Type('string'), null, [], null, self::LINE));
    $class->declare(new Property(['private'], 'b', new Type('string'), null, [], null, self::LINE));
    $class->declare(new Property(['private'], 'c', new Type('int'), null, [], null, self::LINE));

    $this->assertParsed([$class], 'class A { private string $a, $b, int $c; }');
  }

  #[@test]
  public function typed_constant() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Constant([], 'T', new Type('int'), new Literal('1', self::LINE), self::LINE));

    $this->assertParsed([$class], 'class A { const int T = 1; }');
  }

  #[@test]
  public function typed_constants() {
    $class= new ClassDeclaration([], '\\A', null, [], [], [], null, self::LINE);
    $class->declare(new Constant([], 'T', new Type('int'), new Literal('1', self::LINE), self::LINE));
    $class->declare(new Constant([], 'S', new Type('int'), new Literal('2', self::LINE), self::LINE));
    $class->declare(new Constant([], 'I', new Type('string'), new Literal('"i"', self::LINE), self::LINE));

    $this->assertParsed([$class], 'class A { const int T = 1, S = 2, string I = "i"; }');
  }
}
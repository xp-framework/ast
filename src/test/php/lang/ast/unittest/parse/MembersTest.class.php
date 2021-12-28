<?php namespace lang\ast\unittest\parse;

use lang\ast\Type;
use lang\ast\nodes\{
  Annotations,
  ClassDeclaration,
  Constant,
  InstanceExpression,
  InvokeExpression,
  Literal,
  Method,
  Property,
  ScopeExpression,
  Signature,
  Variable,
  Parameter
};
use lang\ast\types\{IsFunction, IsLiteral, IsNullable, IsUnion, IsValue};
use unittest\{Assert, Test, Values};

class MembersTest extends ParseTest {

  /** @return iterable */
  private function types() {
    yield ['string', new IsLiteral('string')];
    yield ['Test', new IsValue('Test')];
    yield ['\unittest\Test', new IsValue('\unittest\Test')];
    yield ['?string', new IsNullable(new IsLiteral('string'))];
    yield ['?(string|int)', new IsNullable(new IsUnion([new IsLiteral('string'), new IsLiteral('int')]))];
    yield ['string|int', new IsUnion([new IsLiteral('string'), new IsLiteral('int')])];
    yield ['string|function(): void', new IsUnion([new IsLiteral('string'), new IsFunction([], new IsLiteral('void'))])];
    yield ['string|(function(): void)', new IsUnion([new IsLiteral('string'), new IsFunction([], new IsLiteral('void'))])];
    yield ['function(): string', new IsFunction([], new IsLiteral('string'))];
    yield ['(function(): string)', new IsFunction([], new IsLiteral('string'))];
  }

  #[Test]
  public function private_instance_property() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Property(['private'], 'a', null, null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private $a; }');
  }

  #[Test]
  public function private_instance_properties() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Property(['private'], 'a', null, null, null, null, self::LINE));
    $class->declare(new Property(['private'], 'b', null, null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private $a, $b; }');
  }

  #[Test]
  public function private_instance_method() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Method(['private'], 'a', new Signature([], null, self::LINE), [], null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private function a() { } }');
  }

  #[Test]
  public function private_static_method() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Method(['private', 'static'], 'a', new Signature([], null, self::LINE), [], null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private static function a() { } }');
  }

  #[Test]
  public function class_constant() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Constant([], 'T', null, new Literal('1', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { const T = 1; }');
  }

  #[Test]
  public function class_constants() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Constant([], 'T', null, new Literal('1', self::LINE), null, null, self::LINE));
    $class->declare(new Constant([], 'S', null, new Literal('2', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { const T = 1, S = 2; }');
  }

  #[Test]
  public function private_class_constant() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Constant(['private'], 'T', null, new Literal('1', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private const T = 1; }');
  }

  #[Test, Values('types')]
  public function method_with_typed_parameter($declaration, $expected) {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $params= [new Parameter('param', $expected, null, false, false, null, null)];
    $class->declare(new Method(['public'], 'a', new Signature($params, null, self::LINE), [], null, null, self::LINE));

    $this->assertParsed([$class], 'class A { public function a('.$declaration.' $param) { } }');
  }

  #[Test, Values('types')]
  public function method_with_return_type($declaration, $expected) {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Method(['public'], 'a', new Signature([], $expected, self::LINE), [], null, null, self::LINE));

    $this->assertParsed([$class], 'class A { public function a(): '.$declaration.' { } }');
  }

  #[Test]
  public function method_with_annotation() {
    $annotations= new Annotations(['Test' => []], self::LINE);
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Method(['public'], 'a', new Signature([], null, self::LINE), [], $annotations, null, self::LINE));

    $this->assertParsed([$class], 'class A { #[Test] public function a() { } }');
  }

  #[Test]
  public function method_with_annotations() {
    $annotations= new Annotations(['Test' => [], 'Ignore' => [new Literal('"Not implemented"', self::LINE)]], self::LINE);
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Method(['public'], 'a', new Signature([], null, self::LINE), [], $annotations, null, self::LINE));

    $this->assertParsed([$class], 'class A { #[Test, Ignore("Not implemented")] public function a() { } }');
  }

  #[Test]
  public function instance_property_access() {
    $this->assertParsed(
      [new InstanceExpression(new Variable('a', self::LINE), new Literal('member', self::LINE), self::LINE)],
      '$a->member;'
    );
  }

  #[Test]
  public function dynamic_instance_property_access_via_variable() {
    $this->assertParsed(
      [new InstanceExpression(new Variable('a', self::LINE), new Variable('member', self::LINE), self::LINE)],
      '$a->$member;'
    );
  }

  #[Test]
  public function dynamic_instance_property_access_via_variable_expression() {
    $this->assertParsed(
      [new InstanceExpression(new Variable('a', self::LINE), new Variable('member', self::LINE), self::LINE)],
      '$a->{$member};'
    );
  }

  #[Test]
  public function dynamic_instance_property_access_via_complex_expression() {
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

  #[Test]
  public function static_property_access() {
    $this->assertParsed(
      [new ScopeExpression('\\A', new Variable('member', self::LINE), self::LINE)],
      'A::$member;'
    );
  }

  #[Test, Values(['self', 'parent', 'static'])]
  public function scope_resolution($scope) {
    $this->assertParsed(
      [new ScopeExpression($scope, new Literal('class', self::LINE), self::LINE)],
      $scope.'::class;'
    );
  }

  #[Test]
  public function class_resolution() {
    $this->assertParsed(
      [new ScopeExpression('\\A', new Literal('class', self::LINE), self::LINE)],
      'A::class;'
    );
  }

  #[Test]
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

  #[Test]
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

  #[Test]
  public function typed_property() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Property(['private'], 'a', new Type('string'), null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private string $a; }');
  }

  #[Test]
  public function typed_property_with_value() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Property(['private'], 'a', new Type('string'), new Literal('"test"', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private string $a = "test"; }');
  }

  #[Test]
  public function typed_properties() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Property(['private'], 'a', new Type('string'), null, null, null, self::LINE));
    $class->declare(new Property(['private'], 'b', new Type('string'), null, null, null, self::LINE));
    $class->declare(new Property(['private'], 'c', new Type('int'), null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private string $a, $b, int $c; }');
  }

  #[Test]
  public function readonly_property() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Property(['public', 'readonly'], 'a', new Type('int'), null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { public readonly int $a; }');
  }

  #[Test]
  public function typed_constant() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Constant([], 'T', new Type('int'), new Literal('1', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { const int T = 1; }');
  }

  #[Test]
  public function typed_constants() {
    $class= new ClassDeclaration([], '\\A', null, [], [], null, null, self::LINE);
    $class->declare(new Constant([], 'T', new Type('int'), new Literal('1', self::LINE), null, null, self::LINE));
    $class->declare(new Constant([], 'S', new Type('int'), new Literal('2', self::LINE), null, null, self::LINE));
    $class->declare(new Constant([], 'I', new Type('string'), new Literal('"i"', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { const int T = 1, S = 2, string I = "i"; }');
  }
}
<?php namespace lang\ast\unittest\parse;

use lang\ast\Type;
use lang\ast\nodes\{
  Annotations,
  Block,
  ClassDeclaration,
  Constant,
  Expression,
  Hook,
  InstanceExpression,
  InvokeExpression,
  Literal,
  Method,
  Property,
  ReturnStatement,
  ScopeExpression,
  Signature,
  Variable,
  Parameter
};
use lang\ast\types\{IsFunction, IsLiteral, IsNullable, IsUnion, IsValue, IsGeneric};
use test\{Assert, Test, Values};

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
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Property(['private'], 'a', null, null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private $a; }');
  }

  #[Test]
  public function final_instance_property() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Property(['public', 'final'], 'a', new IsLiteral('string'), null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { public final string $a; }');
  }

  #[Test]
  public function private_instance_properties() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Property(['private'], 'a', null, null, null, null, self::LINE));
    $class->declare(new Property(['private'], 'b', null, null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private $a, $b; }');
  }

  #[Test]
  public function private_instance_method() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Method(['private'], 'a', new Signature([], null, false, self::LINE), [], null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private function a() { } }');
  }

  #[Test]
  public function private_static_method() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Method(['private', 'static'], 'a', new Signature([], null, false, self::LINE), [], null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private static function a() { } }');
  }

  #[Test]
  public function method_returning_reference() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Method(['private', 'static'], 'a', new Signature([], null, true, self::LINE), [], null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private static function &a() { } }');
  }

  #[Test]
  public function class_constant() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Constant([], 'T', null, new Literal('1', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { const T = 1; }');
  }

  #[Test]
  public function class_constants() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Constant([], 'T', null, new Literal('1', self::LINE), null, null, self::LINE));
    $class->declare(new Constant([], 'S', null, new Literal('2', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { const T = 1, S = 2; }');
  }

  #[Test]
  public function private_class_constant() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Constant(['private'], 'T', null, new Literal('1', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private const T = 1; }');
  }

  #[Test, Values(from: 'types')]
  public function method_with_typed_parameter($declaration, $expected) {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $params= [new Parameter('param', $expected, null, false, false, null, null, null, self::LINE)];
    $class->declare(new Method(['public'], 'a', new Signature($params, null, false, self::LINE), [], null, null, self::LINE));

    $this->assertParsed([$class], 'class A { public function a('.$declaration.' $param) { } }');
  }

  #[Test, Values(from: 'types')]
  public function method_with_return_type($declaration, $expected) {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Method(['public'], 'a', new Signature([], $expected, false, self::LINE), [], null, null, self::LINE));

    $this->assertParsed([$class], 'class A { public function a(): '.$declaration.' { } }');
  }

  #[Test]
  public function method_with_annotation() {
    $annotations= new Annotations(['Test' => []], self::LINE);
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Method(['public'], 'a', new Signature([], null, false, self::LINE), [], $annotations, null, self::LINE));

    $this->assertParsed([$class], 'class A { #[Test] public function a() { } }');
  }

  #[Test]
  public function method_with_annotations() {
    $annotations= new Annotations(['Test' => [], 'Ignore' => [new Literal('"Not implemented"', self::LINE)]], self::LINE);
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Method(['public'], 'a', new Signature([], null, false, self::LINE), [], $annotations, null, self::LINE));

    $this->assertParsed([$class], 'class A { #[Test, Ignore("Not implemented")] public function a() { } }');
  }

  #[Test]
  public function property_with_get_and_set_hooks() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $prop= new Property(['public'], 'a', null, null, null, null, self::LINE);
    $return= new ReturnStatement(new Literal('"Hello"', self::LINE), self::LINE);
    $parameter= new Parameter('value', null, null, false, false, null, null, null, self::LINE);
    $prop->hooks['get']= new Hook([], 'get', new Block([$return], self::LINE), false, null, self::LINE);
    $prop->hooks['set']= new Hook([], 'set', new Block([], self::LINE), false, $parameter, self::LINE);
    $class->declare($prop);

    $this->assertParsed([$class], 'class A { public $a { get { return "Hello"; } set($value) { } } }');
  }

  #[Test]
  public function property_with_short_get_hook() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $prop= new Property(['public'], 'a', null, null, null, null, self::LINE);
    $prop->hooks['get']= new Hook([], 'get', new Literal('"Hello"', self::LINE), false, null, self::LINE);
    $class->declare($prop);

    $this->assertParsed([$class], 'class A { public $a { get => "Hello"; } }');
  }

  #[Test]
  public function property_with_abbreviated_get_hook() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $prop= new Property(['public'], 'a', null, null, null, null, self::LINE);
    $prop->hooks['get']= new Hook([], 'get', new Literal('"Hello"', self::LINE), false, null, self::LINE);
    $class->declare($prop);

    $this->assertParsed([$class], 'class A { public $a => "Hello"; }');
  }

  #[Test]
  public function property_with_typed_hook() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $prop= new Property(['public'], 'a', null, null, null, null, self::LINE);
    $parameter= new Parameter('value', new IsLiteral('string'), null, false, false, null, null, null, self::LINE);
    $prop->hooks['set']= new Hook([], 'set', new Block([], self::LINE), false, $parameter, self::LINE);
    $class->declare($prop);

    $this->assertParsed([$class], 'class A { public $a { set(string $value) { } } }');
  }

  #[Test]
  public function property_with_final_hook() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $prop= new Property(['public'], 'a', null, null, null, null, self::LINE);
    $parameter= new Parameter('value', new IsLiteral('string'), null, false, false, null, null, null, self::LINE);
    $prop->hooks['set']= new Hook(['final'], 'set', new Block([], self::LINE), false, $parameter, self::LINE);
    $class->declare($prop);

    $this->assertParsed([$class], 'class A { public $a { final set(string $value) { } } }');
  }

  #[Test]
  public function abstract_property_with_hook() {
    $class= new ClassDeclaration(['abstract'], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $prop= new Property(['public', 'abstract'], 'a', null, null, null, null, self::LINE);
    $prop->hooks['set']= new Hook([], 'set', null, false, null, self::LINE);
    $class->declare($prop);

    $this->assertParsed([$class], 'abstract class A { public abstract $a { set; } }');
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
    $member= new Expression(new Variable('member', self::LINE), self::LINE);
    $this->assertParsed(
      [new InstanceExpression(new Variable('a', self::LINE), $member, self::LINE)],
      '$a->{$member};'
    );
  }

  #[Test]
  public function dynamic_class_property_access_via_variable_expression() {
    $member= new Variable(new Expression(new Variable('member', self::LINE), self::LINE), self::LINE);
    $this->assertParsed(
      [new ScopeExpression('self', $member, self::LINE)],
      'self::${$member};'
    );
  }

  #[Test]
  public function dynamic_instance_property_access_via_complex_expression() {
    $member= new Expression(
      new InvokeExpression(
        new InstanceExpression(new Variable('field', self::LINE), new Literal('get', self::LINE), self::LINE),
        [new Variable('instance', self::LINE)],
        self::LINE
      ),
      self::LINE
    );
    $this->assertParsed(
      [new InstanceExpression(new Variable('a', self::LINE), $member, self::LINE)],
      '$a->{$field->get($instance)};'
    );
  }

  #[Test]
  public function dynamic_class_property_access_via_complex_expression() {
    $member= new Variable(
      new Expression(
        new InvokeExpression(
          new InstanceExpression(new Variable('field', self::LINE), new Literal('get', self::LINE), self::LINE),
          [new Variable('instance', self::LINE)],
          self::LINE
        ),
        self::LINE
      ),
      self::LINE
    );
    $this->assertParsed(
      [new ScopeExpression('self', $member, self::LINE)],
      'self::${$field->get($instance)};'
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
  public function generic_class_resolution() {
    $this->assertParsed(
      [new ScopeExpression(new IsGeneric(new IsValue('\\A'), [new IsValue('\\T')]), new Literal('class', self::LINE), self::LINE)],
      'A<T>::class;'
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
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Property(['private'], 'a', new Type('string'), null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private string $a; }');
  }

  #[Test]
  public function typed_property_with_value() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Property(['private'], 'a', new Type('string'), new Literal('"test"', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private string $a = "test"; }');
  }

  #[Test]
  public function typed_properties() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Property(['private'], 'a', new Type('string'), null, null, null, self::LINE));
    $class->declare(new Property(['private'], 'b', new Type('string'), null, null, null, self::LINE));
    $class->declare(new Property(['private'], 'c', new Type('int'), null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { private string $a, $b, int $c; }');
  }

  #[Test]
  public function readonly_property() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Property(['public', 'readonly'], 'a', new Type('int'), null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { public readonly int $a; }');
  }

  #[Test]
  public function typed_constant() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Constant([], 'T', new Type('int'), new Literal('1', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { const int T = 1; }');
  }

  #[Test]
  public function typed_constants() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Constant([], 'T', new Type('int'), new Literal('1', self::LINE), null, null, self::LINE));
    $class->declare(new Constant([], 'S', new Type('int'), new Literal('2', self::LINE), null, null, self::LINE));
    $class->declare(new Constant([], 'I', new Type('string'), new Literal('"i"', self::LINE), null, null, self::LINE));

    $this->assertParsed([$class], 'class A { const int T = 1, S = 2, string I = "i"; }');
  }

  #[Test]
  public function asymmetric_property() {
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Property(['public', 'private(set)'], 'a', new Type('int'), null, null, null, self::LINE));

    $this->assertParsed([$class], 'class A { public private(set) int $a; }');
  }

  #[Test]
  public function asymmetric_property_as_constructor_argument() {
    $params= [new Parameter('a', new IsLiteral('int'), null, false, false, ['private(set)'], null, null, self::LINE)];
    $class= new ClassDeclaration([], new IsValue('\\A'), null, [], [], null, null, self::LINE);
    $class->declare(new Method(['public'], '__construct', new Signature($params, null, false, self::LINE), [], null, null, self::LINE));

    $this->assertParsed([$class], 'class A { public function __construct(private(set) int $a) { } }');
  }
}
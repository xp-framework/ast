<?php namespace lang\ast\unittest;

use lang\ast\nodes\{NamespaceDeclaration, ClassDeclaration};
use lang\ast\types\{Compiled, Reflection};
use lang\ast\{ParseTree, Scope};
use unittest\Assert;

class ParseTreeTest {

  #[@test, @values([
  #  [[]],
  #  [[new NamespaceDeclaration('test')]],
  #])]
  public function can_create_from($input) {
    new ParseTree($input);
  }

  #[@test]
  public function can_create_from_iterator() {
    $it= function() { yield new NamespaceDeclaration('test'); }; 
    new ParseTree($it());
  }

  #[@test]
  public function scope() {
    $scope= new Scope(null);
    Assert::equals($scope, (new ParseTree([], $scope))->scope());
  }

  #[@test]
  public function file() {
    Assert::equals('test.php', (new ParseTree([], null, 'test.php'))->file());
  }

  #[@test, @values([
  #  [[]],
  #  [[new NamespaceDeclaration('test')]],
  #])]
  public function children($list) {
    Assert::equals($list, (new ParseTree($list))->children());
  }

  #[@test]
  public function types_initially_empty() {
    $scope= new Scope(null);
    Assert::equals([], (new ParseTree([], $scope))->types());
  }

  #[@test, @values([
  #  [null, '\\Test'],
  #  ['com\\example', '\\com\\example\\Test'],
  #])]
  public function types_in($package, $expected) {
    $scope= new Scope(null);
    $scope->package($package);
    $scope->declare('Test', new ClassDeclaration([], 'Test', null, [], []));

    Assert::equals([$expected => $scope->type('Test')], (new ParseTree([], $scope))->types());
  }

  #[@test]
  public function declared_type() {
    $scope= new Scope(null);
    $scope->declare('Test', new ClassDeclaration([], 'Test', null, [], []));

    Assert::instance(Compiled::class, (new ParseTree([], $scope))->type('Test'));
  }

  #[@test]
  public function core_type() {
    $scope= new Scope(null);

    Assert::instance(Reflection::class, (new ParseTree([], $scope))->type('\\lang\\Value'));
  }

  #[@test]
  public function non_existant_type() {
    $scope= new Scope(null);

    Assert::null((new ParseTree([], $scope))->type('Unknown'));
  }
}
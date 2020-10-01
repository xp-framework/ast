<?php namespace lang\ast\unittest;

use lang\ast\nodes\{ClassDeclaration, NamespaceDeclaration};
use lang\ast\types\{Compiled, Reflection};
use lang\ast\{ParseTree, Scope};
use unittest\{Assert, Test, Values};

class ParseTreeTest {

  #[Test]
  public function can_create_from_empty() {
    new ParseTree([]);
  }

  #[Test]
  public function can_create_from_input() {
    new ParseTree([new NamespaceDeclaration('test')]);
  }

  #[Test]
  public function can_create_from_iterator() {
    $it= function() { yield new NamespaceDeclaration('test'); }; 
    new ParseTree($it());
  }

  #[Test]
  public function scope() {
    $scope= new Scope(null);
    Assert::equals($scope, (new ParseTree([], $scope))->scope());
  }

  #[Test]
  public function file() {
    Assert::equals('test.php', (new ParseTree([], null, 'test.php'))->file());
  }

  #[Test]
  public function empty_children() {
    Assert::equals([], (new ParseTree([]))->children());
  }

  #[Test]
  public function children() {
    $namespace= new NamespaceDeclaration('test');
    Assert::equals([$namespace], (new ParseTree([$namespace]))->children());
  }

  #[Test]
  public function types_initially_empty() {
    $scope= new Scope(null);
    Assert::equals([], iterator_to_array((new ParseTree([], $scope))->types()));
  }

  #[Test, Values([[null, '\\Test'], ['com\\example', '\\com\\example\\Test'],])]
  public function types_in($package, $expected) {
    $scope= new Scope(null);
    $scope->package($package);
    $class= new ClassDeclaration([], 'Test');

    Assert::equals([$expected => $class], iterator_to_array((new ParseTree([$class], $scope))->types()));
  }
}
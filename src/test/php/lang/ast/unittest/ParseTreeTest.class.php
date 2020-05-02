<?php namespace lang\ast\unittest;

use lang\ast\nodes\NamespaceDeclaration;
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
}
<?php namespace lang\ast\unittest;

use lang\ast\ParseTree;
use lang\ast\nodes\{
  NamespaceDeclaration,
  UseStatement,
  TraitDeclaration,
  ClassDeclaration,
  InterfaceDeclaration,
  Method,
  Property,
  Signature
};
use unittest\TestCase;

class ParseTreeTest extends TestCase {

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
  public function query_toplevel_node() {
    $namespace= new NamespaceDeclaration('test');
    $fixture= new ParseTree([$namespace, new UseStatement(null, ['\\util\\Date' => null])]);

    $this->assertEquals([$namespace], iterator_to_array($fixture->query('/namespace')));
  }

  #[@test]
  public function query_toplevel_nodes() {
    $imports= [new UseStatement(null, ['\\util\\Date' => null]), new UseStatement(null, ['\\lang\\Value' => null])];
    $fixture= new ParseTree(array_merge([new NamespaceDeclaration('test')], $imports));

    $this->assertEquals($imports, iterator_to_array($fixture->query('/import')));
  }

  #[@test]
  public function query_nested_nodes() {
    $methods= [
      'a()' => new Method(['public'], 'a', new Signature([], null), []),
      'b()' => new Method(['public'], 'b', new Signature([], null), []),
    ];
    $properties= [
      '$a' => new Property(['private'], 'a', null),
    ];
    $fixture= new ParseTree([new TraitDeclaration([], 'Test', array_merge($methods, $properties))]);

    $this->assertEquals(array_values($methods), iterator_to_array($fixture->query('/trait/method')));
  }

  #[@test]
  public function query_nested_without_chidren() {
    $fixture= new ParseTree([new NamespaceDeclaration('test')]);

    $this->assertEquals([], iterator_to_array($fixture->query('/namespace/does-not-have-children')));
  }

  #[@test]
  public function query_context() {
    $methods= [
      'a()' => new Method(['public'], 'a', new Signature([], null), []),
      'b()' => new Method(['public'], 'b', new Signature([], null), []),
    ];
    $type= new TraitDeclaration([], 'Test', $methods);
    $fixture= new ParseTree([$type]);

    $this->assertEquals(array_values($methods), iterator_to_array($fixture->query('/method', $type)));
  }

  #[@test]
  public function query_all() {
    $types= [
      new TraitDeclaration([], 'WithDatabase', []),
      new ClassDeclaration([], 'Application', null, [], []),
      new InterfaceDeclaration([], 'Closeable', [], []),
    ];
    $fixture= new ParseTree($types);

    $this->assertEquals($types, iterator_to_array($fixture->query('/interface|trait|class')));
  }

  #[@test]
  public function query_type() {
    $types= [
      new TraitDeclaration([], 'WithDatabase', []),
      new ClassDeclaration([], 'Application', null, [], []),
      new InterfaceDeclaration([], 'Closeable', [], []),
    ];
    $fixture= new ParseTree($types);

    $this->assertEquals($types, iterator_to_array($fixture->query('/@type')));
  }

  #[@test]
  public function collect() {
    $methods= [
      'a()' => new Method(['public'], 'a', new Signature([], null), []),
      'b()' => new Method(['public'], 'b', new Signature([], null), []),
    ];
    $properties= [
      '$a' => new Property(['private'], 'a', null),
    ];
    $fixture= new ParseTree([new TraitDeclaration([], 'Test', array_merge($methods, $properties))]);
    $result= $fixture->collect(['methods' => [], 'properties' => []], [
      '/*/method'   => function(&$result, $method) { $result['methods'][]= $method; },
      '/*/property' => function(&$result, $property) { $result['properties'][]= $property; },
    ]);

    $this->assertEquals(
      ['methods' => array_values($methods), 'properties' => array_values($properties)],
      $result
    );
  }
}
<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{NamespaceDeclaration, UseStatement};
use unittest\{Assert, Test};

class NamespacesTest extends ParseTest {

  #[Test]
  public function simple_namespace() {
    $this->assertParsed(
      [new NamespaceDeclaration('test', self::LINE)],
      'namespace test;'
    );
  }

  #[Test]
  public function compound_namespace() {
    $this->assertParsed(
      [new NamespaceDeclaration('lang\\ast', self::LINE)],
      'namespace lang\\ast;'
    );
  }

  #[Test]
  public function use_statement() {
    $this->assertParsed(
      [new UseStatement(null, ['lang\ast\Parse' => null], self::LINE)],
      'use lang\\ast\\Parse;'
    );
  }

  #[Test]
  public function use_with_alias() {
    $this->assertParsed(
      [new UseStatement(null, ['lang\ast\Parse' => 'P'], self::LINE)],
      'use lang\\ast\\Parse as P;'
    );
  }

  #[Test]
  public function use_with_types_separated_by_commas() {
    $this->assertParsed(
      [new UseStatement(null, ['lang\\ast\\Parse' => null, 'lang\\ast\\Emitter' => null], self::LINE)],
      'use lang\\ast\\Parse, lang\\ast\\Emitter;'
    );
  }

  #[Test]
  public function use_global() {
    $this->assertParsed(
      [new UseStatement(null, ['Iterator' => null], self::LINE)],
      'use Iterator;'
    );
  }

  #[Test]
  public function use_globals() {
    $this->assertParsed(
      [new UseStatement(null, ['Iterator' => null, 'Traversable' => null], self::LINE)],
      'use Iterator, Traversable;'
    );
  }

  #[Test]
  public function grouped_use_statement() {
    $this->assertParsed(
      [new UseStatement(null, ['lang\\ast\\Parse' => null, 'lang\\ast\\Emitter' => null], self::LINE)],
      'use lang\\ast\\{Parse, Emitter};'
    );
  }

  #[Test]
  public function grouped_use_with_relative() {
    $this->assertParsed(
      [new UseStatement(null, ['lang\\ast\\Parse' => null, 'lang\\ast\\emit\\Result' => null], self::LINE)],
      'use lang\\ast\\{Parse, emit\\Result};'
    );
  }

  #[Test]
  public function grouped_use_with_alias() {
    $this->assertParsed(
      [new UseStatement(null, ['lang\\ast\\Parse' => 'P'], self::LINE)],
      'use lang\\ast\\{Parse as P};'
    );
  }
}
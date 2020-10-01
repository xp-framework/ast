<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{NamespaceDeclaration, Start};
use unittest\{Assert, Test};

class StartTokensTest extends ParseTest {

  #[Test]
  public function php() {
    $this->assertParsed(
      [new Start('php', self::LINE), new NamespaceDeclaration('test', self::LINE)],
      '<?php namespace test;'
    );
  }

  #[Test]
  public function hack() {
    $this->assertParsed(
      [new Start('hh', self::LINE), new NamespaceDeclaration('test', self::LINE)],
      '<?hh namespace test;'
    );
  }
}
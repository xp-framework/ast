<?php namespace lang\ast\unittest\parse;

use lang\ast\Errors;
use lang\ast\nodes\{Directives, NamespaceDeclaration, Scalar};
use test\{Assert, Expect, Test};

class StartTokensTest extends ParseTest {

  #[Test]
  public function php() {
    $this->assertParsed(
      [new NamespaceDeclaration('test', self::LINE)],
      '<?php namespace test;'
    );
  }

  #[Test]
  public function declare() {
    $declare= ['strict_types' => new Scalar('1', 'integer', self::LINE)];
    $this->assertParsed(
      [new Directives($declare, self::LINE)],
      '<?php declare(strict_types = 1);'
    );
  }

  #[Test]
  public function declare_with_multiple() {
    $declare= [
      'strict_types' => new Scalar('1', 'integer', self::LINE),
      'encoding'     => new Scalar('"UTF-8"', 'string', self::LINE),
    ];
    $this->assertParsed(
      [new Directives($declare, self::LINE)],
      '<?php declare(strict_types = 1, encoding = "UTF-8");'
    );
  }

  #[Test, Expect(class: Errors::class, message: '/Unexpected syntax hh, expecting php in <\\?/')]
  public function hack() {
    $this->parse('<?hh namespace test;')->tree();
  }
}
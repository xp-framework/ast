<?php namespace lang\ast\unittest\parse;

use lang\ast\Errors;
use lang\ast\nodes\{NamespaceDeclaration, Directives, Literal};
use unittest\{Assert, Test};

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
    $declare= ['strict_types' => new Literal('1', self::LINE)];
    $this->assertParsed(
      [new Directives($declare, self::LINE)],
      '<?php declare(strict_types = 1);'
    );
  }

  #[Test]
  public function declare_with_multiple() {
    $declare= ['strict_types' => new Literal('1', self::LINE), 'encoding' => new Literal('"UTF-8"', self::LINE)];
    $this->assertParsed(
      [new Directives($declare, self::LINE)],
      '<?php declare(strict_types = 1, encoding = "UTF-8");'
    );
  }

  #[Test, Expect(class: Errors::class, withMessage: 'Unexpected syntax hh, expecting php in <?')]
  public function hack() {
    $this->parse('<?hh namespace test;')->tree();
  }
}
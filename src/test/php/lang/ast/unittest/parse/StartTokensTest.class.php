<?php namespace lang\ast\unittest\parse;

use lang\ast\Errors;
use lang\ast\nodes\NamespaceDeclaration;
use unittest\{Assert, Test};

class StartTokensTest extends ParseTest {

  #[Test]
  public function php() {
    $this->assertParsed(
      [new NamespaceDeclaration('test', self::LINE)],
      '<?php namespace test;'
    );
  }

  #[Test, Expect(class: Errors::class, withMessage: 'Unexpected syntax hh, expecting php in <?')]
  public function hack() {
    $this->parse('<?hh namespace test;')->tree();
  }
}
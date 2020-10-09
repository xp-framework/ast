<?php namespace lang\ast\unittest;

use lang\FormatException;
use lang\ast\Tokens;
use unittest\{Assert, Expect, Test, Values};

class TokensTest {

  /**
   * Assertion helper
   *
   * @param  [:var][] $expected
   * @param  lang.ast.Tokens $tokens
   * @throws unittest.AssertionFailedError
   * @return void
   */
  private function assertTokens($expected, $tokens) {
    $actual= [];
    foreach ($tokens as $type => $value) {
      $actual[]= [$type => $value[0]];
    }
    Assert::equals($expected, $actual);
  }

  #[Test]
  public function can_create() {
    new Tokens('test');
  }

  #[Test, Values(['""', "''", "'\\\\'", '"Test"', "'Test'", "'Test\''", "'\\\\\\''",])]
  public function string_literals($input) {
    $this->assertTokens([['string' => $input]], new Tokens($input));
  }

  #[Test, Expect(class: FormatException::class, withMessage: '/Unclosed string literal/'), Values(['"', "'", '"Test', "'Test"])]
  public function unclosed_string_literals($input) {
    $t= (new Tokens($input))->getIterator(); 
    $t->current();
  }

  #[Test, Values(['0', '1', '1_000_000_000'])]
  public function integer_literal($input) {
    $this->assertTokens([['integer' => str_replace('_', '', $input)]], new Tokens($input));
  }

  #[Test, Values(['0.0', '6.1', '.5', '107_925_284.88'])]
  public function float_literal($input) {
    $this->assertTokens([['decimal' => str_replace('_', '', $input)]], new Tokens($input));
  }

  #[Test, Values(['$a', '$_', '$input'])]
  public function variables($input) {
    $this->assertTokens([['variable' => $input]], new Tokens($input));
  }

  #[Test, Values(['+', '-', '*', '/', '**', '==', '!=', '<=', '>=', '<=>', '===', '!==', '=>', '->',])]
  public function operators($input) {
    $this->assertTokens([['operator' => $input]], new Tokens($input));
  }

  #[Test]
  public function annotation() {
    $this->assertTokens(
      [['operator' => '#['], ['name' => 'Test'], ['operator' => ']']],
      new Tokens('#[Test]')
    );
  }

  #[Test]
  public function regular_comment() {
    $this->assertTokens([['comment' => '// Comment']], new Tokens('// Comment'));
  }

  #[Test]
  public function oneline_comment() {
    $this->assertTokens([['comment' => '# Comment']], new Tokens('# Comment'));
  }

  #[Test]
  public function inline_comment() {
    $this->assertTokens([['comment' => '/* Comment */']], new Tokens('/* Comment */'));
  }

  #[Test]
  public function apidoc_comment() {
    $this->assertTokens([['comment' => '/** Test */']], new Tokens('/** Test */'));
  }
}
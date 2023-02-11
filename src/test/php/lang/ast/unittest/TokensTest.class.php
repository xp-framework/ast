<?php namespace lang\ast\unittest;

use lang\FormatException;
use lang\ast\{Language, Tokens};
use test\{Assert, Before, Expect, Test, Values};

class TokensTest {
  private $language;

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
    foreach ($tokens->iterator($this->language) as $token) {
      $actual[]= [$token->kind => $token->value];
    }
    Assert::equals($expected, $actual);
  }

  #[Before]
  public function language() {
    $this->language= Language::named('PHP');
  }

  #[Test]
  public function can_create() {
    new Tokens('test');
  }

  #[Test, Values(['""', "''", "'\\\\'", '"Test"', "'Test'", "'Test\''", "'\\\\\\''",])]
  public function string_literals($input) {
    $this->assertTokens([['string' => $input]], new Tokens($input));
  }

  #[Test, Expect(class: FormatException::class, message: '/Unclosed string literal/'), Values(['"', "'", '"Test', "'Test"])]
  public function unclosed_string_literals($input) {
    (new Tokens($input))->iterator($this->language)->current();
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
    $this->assertTokens([['apidoc' => '/** Test */']], new Tokens('/** Test */'));
  }
}
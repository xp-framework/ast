<?php namespace lang\ast\unittest;

use lang\ast\{Language, Tokens};
use test\{Assert, Before, Test};

class LineNumberTest {
  private $language;

  /**
   * Assertion helper
   *
   * @param  [:var][] $expected
   * @param  lang.ast.Tokens $tokens
   * @throws unittest.AssertionFailedError
   * @return void
   */
  private function assertPositions($expected, $tokens) {
    $actual= [];
    foreach ($tokens->iterator($this->language) as $token) {
      $actual[]= [$token->value => $token->line];
    }
    Assert::equals($expected, $actual);
  }

  #[Before]
  public function language() {
    $this->language= Language::named('PHP');
  }

  #[Test]
  public function starts_with_line_number_one() {
    $this->assertPositions(
      [['HERE' => 1]],
      new Tokens("HERE")
    );
  }

  #[Test]
  public function unix_lines() {
    $this->assertPositions(
      [['LINE1' => 1], ['LINE2' => 2]],
      new Tokens("LINE1\nLINE2")
    );
  }

  #[Test]
  public function windows_lines() {
    $this->assertPositions(
      [['LINE1' => 1], ['LINE2' => 2]],
      new Tokens("LINE1\r\nLINE2")
    );
  }

  #[Test]
  public function after_regular_comment() {
    $this->assertPositions(
      [['// Comment' => 1], ['HERE' => 2]],
      new Tokens("// Comment\nHERE")
    );
  }

  #[Test]
  public function after_apidoc_comment() {
    $this->assertPositions(
      [['/** Apidoc */' => 1], ['HERE' => 2]],
      new Tokens("/** Apidoc */\nHERE")
    );
  }

  #[Test]
  public function multi_line_apidoc_comment() {
    $this->assertPositions(
      [["/** LINE1\nLINE2 */" => 1], ['HERE' => 3]],
      new Tokens("/** LINE1\nLINE2 */\nHERE")
    );
  }

  #[Test]
  public function multi_line_apidoc_comment_is_not_trimmed() {
    $this->assertPositions(
      [["/** Apidoc\n */" => 1], ['HERE' => 3]],
      new Tokens("/** Apidoc\n */\nHERE")
    );
  }

  #[Test]
  public function multi_line_string() {
    $this->assertPositions(
      [["'STRING\n'" => 1], ['HERE' => 3]],
      new Tokens("'STRING\n'\nHERE")
    );
  }
}
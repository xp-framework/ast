<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{ClassDeclaration, Literal};
use unittest\{Assert, Test};

class CommentTest extends ParseTest {

  #[Test]
  public function oneline_double_slash() {
    $this->assertParsed([new Literal('"test"', 3)], '
      // This is a comment
      "test";
    ');
  }

  #[Test]
  public function oneline_double_slash_at_end() {
    $this->assertParsed([new Literal('"test"', 2)], '
      "test";  // This is a comment
    ');
  }

  #[Test]
  public function two_oneline_double_slash() {
    $this->assertParsed([new Literal('"test"', 4)], '
      // This is a comment
      // This is another
      "test";
    ');
  }

  #[Test]
  public function oneline_hashtag() {
    $this->assertParsed([new Literal('"test"', 3)], '
      # This is a comment
      "test";
    ');
  }

  #[Test]
  public function oneline_hashtag_at_end() {
    $this->assertParsed([new Literal('"test"', 2)], '
      "test";  # This is a comment
    ');
  }

  #[Test]
  public function two_oneline_hashtags() {
    $this->assertParsed([new Literal('"test"', 4)], '
      # This is a comment
      # This is another
      "test";
    ');
  }

  #[Test]
  public function oneline_slash_asterisk() {
    $this->assertParsed([new Literal('"test"', 3)], '
      /* This is a comment */
      "test";
    ');
  }

  #[Test]
  public function oneline_slash_asterisk_at_end() {
    $this->assertParsed([new Literal('"test"', 2)], '
      "test";  /* This is a comment */
    ');
  }

  #[Test]
  public function oneline_slash_asterisk_inbetween() {
    $this->assertParsed([new Literal('"before"', 2), new Literal('"after"', 2)], '
      "before"; /* This is a comment */ "after";
    ');
  }

  #[Test]
  public function multiline_slash_asterisk() {
    $this->assertParsed([new Literal('"test"', 5)], '
      /* This is a comment
       * spanning multiple lines.
       */
      "test";
    ');
  }

  #[Test]
  public function apidoc_comment_attached_to_next_node() {
    $this->assertParsed([new ClassDeclaration([], '\\T', null, [], [], [], '/** @see http://example.org/ */', 3)], '
      /** @see http://example.org/ */
      class T { }
    ');
  }
}
<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{Annotations, ClassDeclaration, Comment, Constant, Property, Method, Signature, Literal};
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
  public function apidoc_comment_at_end_discarded() {
    $this->assertParsed([new Literal('"test"', 2)], '
      "test";  /** Discarded */
    ');
  }

  #[Test]
  public function apidoc_comment_after_class_name_discarded() {
    $this->assertParsed([new ClassDeclaration([], '\\T', null, [], [], [], null, 2)], '
      class T /** Discarded */ { }
    ');
  }

  #[Test]
  public function apidoc_comment_attached_to_next_node() {
    $this->assertParsed([new ClassDeclaration([], '\\T', null, [], [], [], new Comment('/** @api */', 2), 3)], '
      /** @api */
      class T { }
    ');
  }

  #[Test]
  public function apidoc_comment_and_annotations() {
    $this->assertParsed([new ClassDeclaration([], '\\T', null, [], [], new Annotations(['Test' => []], 3), new Comment('/** @api */', 2), 4)], '
      /** @api */
      #[Test]
      class T { }
    ');
  }

  #[Test]
  public function apidoc_comment_attached_to_next_constant() {
    $class= new ClassDeclaration([], '\\T', null, [], [], [], null, 2);
    $class->declare(new Constant(['public'], 'FIXTURE', null, new Literal('1', 4), [], new Comment('/** @api */', 3), 4));

    $this->assertParsed([$class], '
      class T {
        /** @api */
        public const FIXTURE = 1;
      }
    ');
  }

  #[Test]
  public function apidoc_comment_attached_to_next_property() {
    $class= new ClassDeclaration([], '\\T', null, [], [], [], null, 2);
    $class->declare(new Property(['public'], 'fixture', null, null, [], new Comment('/** @api */', 3), 4));

    $this->assertParsed([$class], '
      class T {
        /** @api */
        public $fixture;
      }
    ');
  }

  #[Test]
  public function apidoc_comment_attached_to_next_method() {
    $class= new ClassDeclaration([], '\\T', null, [], [], [], null, 2);
    $class->declare(new Method(['public'], '__construct', new Signature([], null, 4), [], [], new Comment('/** @api */', 3), 3));

    $this->assertParsed([$class], '
      class T {
        /** @api */
        public function __construct() { }
      }
    ');
  }
}
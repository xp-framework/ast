<?php namespace lang\ast\nodes;

use lang\ast\Node;

abstract class Annotated extends Node {
  public $annotations;
  public $comment= null;

  /**
   * Attach a comment and modify line to include the comment.
   *
   * @param  lang.ast.nodes.Comment|string $comment
   * @return self
   */
  public function attach($comment) {
    if ('' === $comment) {
      $this->comment= null;
    } else if ($comment instanceof Comment) {
      $this->comment= $comment->ending($this->line);
      $this->line= $comment->line;
    } else {
      $declaration= '/' === $comment[0] ? $comment : '/** '.str_replace("\n", "\n * ", $comment).' */';
      $this->comment= new Comment($declaration, $this->line);
      $this->line+= substr_count($comment, "\n") + 1;
    }
    return $this;
  }

  /**
   * Returns an annotation for a given name, or NULL if no annotation
   * exists by that name.
   *
   * @param  string $name
   * @return lang.ast.nodes.Annotation
   */
  public function annotation($name) {
    return array_key_exists($name, $this->annotations)
      ? new Annotation($name, $this->annotations[$name])
      : null
    ;
  }

  /**
   * Checks whether this node is of a given kind
   *
   * @param  string $kind
   * @return bool
   */
  public function is($kind) {
    return '@annotated' === $kind;
  }
}
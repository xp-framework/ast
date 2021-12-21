<?php namespace lang\ast\nodes;

use lang\ast\Node;

abstract class Annotated extends Node {
  public $annotations;
  public $comment= null;

  /**
   * Attach a comment and modify line to include the comment.
   *
   * @param  lang.ast.nodes.Comment|string $comment
   * @return void
   */
  public function attach($comment) {
    if ('' === $comment) {
      $this->comment= null;
    } else if ($comment instanceof Comment) {
      $this->comment= $comment;
      $this->line= $comment->line;
    } else {
      $declaration= '/' === $comment[0] ? $comment : '/** '.str_replace("\n", "\n * ", $comment).' */';
      $this->comment= new Comment($declaration, $this->line);
      $this->line+= substr_count($comment, "\n") + 1;
    }
  }

  /**
   * Returns all annotations on this element
   *
   * @return [:lang.ast.nodes.Annotation]
   */
  public function annotations() {
    return $this->annotations ? $this->annotations->all() : [];
  }

  /**
   * Returns an annotation for a given name, or NULL if no annotation
   * exists by that name.
   *
   * @param  string $name
   * @return ?lang.ast.nodes.Annotation
   */
  public function annotation($name) {
    return $this->annotations ? $this->annotations->named($name) : null;
  }

  /**
   * Annotate this element with a given annotation
   *
   * @param  lang.ast.nodes.Annotation $annotation
   * @return self
   */
  public function annotate($annotation) {
    if (null === $this->annotations) $this->annotations= new Annotations([], $this->line);

    $this->annotations->add($annotation);
    return $this;
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
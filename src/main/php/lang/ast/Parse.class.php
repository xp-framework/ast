<?php namespace lang\ast;

use lang\ast\nodes\Comment;

/** A parsing operation for streaming and access via parse tree */
class Parse {
  private $tokens;
  private $errors= [];

  public $language, $file, $token, $scope;
  public $comment= null;
  public $queue= [];

  /**
   * Creates a new parse instance
   *
   * @param  lang.ast.Language $language
   * @param  lang.ast.Tokens $tokens
   * @param  ?lang.ast.Scope $scope
   */
  public function __construct($language, $tokens, ?Scope $scope= null) {
    $this->language= $language;
    $this->tokens= $tokens->iterator($language);
    $this->file= $tokens->source;
    $this->scope= $scope ?? new Scope(null);
  }

  /**
   * Raise an error
   *
   * @param  string $error
   * @param  string $context
   * @param  int $line
   * @return void
   */
  public function raise($message, $context= null, $line= null) {
    $context && $message.= ' in '.$context;
    $this->errors[]= new Error($message, $this->file, $line ?: $this->token->line);
  }

  /**
   * Emit a warning
   *
   * @param  string $error
   * @param  string $context
   * @return void
   */
  public function warn($message, $context= null) {
    $context && $message.= ' ('.$context.')';
    trigger_error($message.' in '.$this->file.' on line '.$this->token->line);
  }

  /**
   * Forward this parser to the next token
   *
   * @return void
   */
  public function forward() {
    if ($this->queue) {
      $this->token= array_shift($this->queue);
      return;
    }

    while ($this->tokens->valid()) {
      $this->token= $this->tokens->current();
      $this->tokens->next();

      // Store apidoc comments, then continue to next token
      if (null === $this->token->symbol) {
        if ('apidoc' === $this->token->kind) {
          $this->comment= new Comment($this->token->value, $this->token->line);
        }
        continue;
      }
      return;
    }

    $this->token= new Token($this->language->symbol('(end)'), null, null, $this->token ? $this->token->line : 1);
  }

  /**
   * Forward expecting a given token, raise an error if another is encountered
   *
   * @param  string $id
   * @param  string $context
   * @return void
   */
  public function expecting($id, $context) {
    if ($id === $this->token->symbol->id) {
      $this->forward();
      return;
    }

    $message= sprintf(
      'Expected "%s", have "%s" in %s',
      $id,
      $this->token->value ?: $this->token->symbol->id,
      $context
    );
    $e= new Error($message, $this->file, $this->token->line);

    // Ensure we stop if we encounter the end
    if (null === $this->token->value) {
      throw $e;
    } else {
      $this->errors[]= $e;
    }
  }

  /**
   * Parses given file, returning AST nodes one by one
   *
   * @return iterable
   * @throws lang.ast.Errors
   */
  public function stream() {
    $this->forward();
    try {
      while (null !== $this->token->value) {
        if ($statement= $this->language->statement($this)) {
          yield $statement;
        }
      }
    } catch (Error $e) {
      $this->errors[]= $e;
    }

    if ($this->errors) {
      throw new Errors($this->errors, $this->file);
    }
  }

  /**
   * Parses given file, returning AST nodes in a tree
   *
   * @return lang.ast.ParseTree
   * @throws lang.ast.Errors
   */
  public function tree() {
    return new ParseTree($this->stream(), $this->scope, $this->file);
  }
}
<?php namespace lang\ast\nodes;

use lang\ast\Node;

class ClassValue extends Annotated {
  public $name, $modifiers, $parent, $implements, $body, $annotations, $comment;

  public function __construct($name, $modifiers, $parent, $implements, $body, $annotations, $comment) {
    $this->name= $name;
    $this->modifiers= $modifiers;
    $this->parent= $parent;
    $this->implements= $implements;
    $this->body= $body;
    $this->annotations= $annotations;
    $this->comment= $comment;
  }

  public function overwrite(Member $member) {
    $lookup= $member->lookup();
    $overwritten= isset($this->body[$lookup]);

    $this->body[$lookup]= new Node(null, $member->kind(), $member);
    return $overwritten;
  }

  public function inject(Member $member) {
    $lookup= $member->lookup();
    if (isset($this->body[$lookup])) return false;

    $this->body[$lookup]= new Node(null, $member->kind(), $member);
    return true;
  }

  public function methods() {
    foreach ($this->body as $node) {
      if ('method' === $node->kind) yield $node->value;
    }
  }

  public function method($name) {
    $lookup= $name.'()';
    return isset($this->body[$lookup]) ? $this->body[$lookup]->value : null;
  }

  public function properties() {
    foreach ($this->body as $node) {
      if ('property' === $node->kind) yield $node->value;
    }
  }

  public function property($name) {
    $lookup= '$'.$name;
    return isset($this->body[$lookup]) ? $this->body[$lookup]->value : null;
  }

  public function constants() {
    foreach ($this->body as $node) {
      if ('const' === $node->kind) yield $node->value;
    }
  }

  public function constant($name) {
    return isset($this->body[$name]) ? $this->body[$name]->value : null;
  }
}
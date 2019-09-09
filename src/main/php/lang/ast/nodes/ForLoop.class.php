<?php namespace lang\ast\nodes;

use lang\ast\Node;

class ForLoop extends Node {
  public $kind= 'for';
  public $initialization, $condition, $loop, $body;

  public function __construct($initialization, $condition, $loop, $body, $line= -1) {
    $this->initialization= $initialization;
    $this->condition= $condition;
    $this->loop= $loop;
    $this->body= $body;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    foreach ($this->initialization as $element) {
      yield $element;
    }
    foreach ($this->condition as $element) {
      yield $element;
    }
    foreach ($this->loop as $element) {
      yield $element;
    }
    foreach ($this->body as $element) {
      yield $element;
    }
  }
}
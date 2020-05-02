<?php namespace lang\ast\nodes;

use lang\ast\Node;

class BinaryExpression extends Node {
  public $kind= 'binary';
  public $left, $operator, $right;

  public function __construct($left, $operator, $right, $line= -1) {
    $this->left= $left;
    $this->operator= $operator;
    $this->right= $right;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->left, $this->right]; }

  public function resolve($scope) {
    switch ($this->operator) {
      case '.': return $this->left->resolve($scope).$this->right->resolve($scope);
      case '+': return $this->left->resolve($scope) + $this->right->resolve($scope);
      case '-': return $this->left->resolve($scope) - $this->right->resolve($scope);
      case '*': return $this->left->resolve($scope) * $this->right->resolve($scope);
      case '/': return $this->left->resolve($scope) / $this->right->resolve($scope);
      case '%': return $this->left->resolve($scope) % $this->right->resolve($scope);
      case '^': return $this->left->resolve($scope) ^ $this->right->resolve($scope);
      case '|': return $this->left->resolve($scope) | $this->right->resolve($scope);
      case '&': return $this->left->resolve($scope) & $this->right->resolve($scope);
      case '**': return $this->left->resolve($scope) ** $this->right->resolve($scope);
      case '?:': return $this->left->resolve($scope) ?: $this->right->resolve($scope);
      case '??': return $this->left->resolve($scope) ?? $this->right->resolve($scope);
      case '<<': return $this->left->resolve($scope) << $this->right->resolve($scope);
      case '>>': return $this->left->resolve($scope) >> $this->right->resolve($scope);
      case '||': return $this->left->resolve($scope) || $this->right->resolve($scope);
      case '&&': return $this->left->resolve($scope) && $this->right->resolve($scope);
      case '==': return $this->left->resolve($scope) == $this->right->resolve($scope);
      case '!=': return $this->left->resolve($scope) != $this->right->resolve($scope);
      case '<': return $this->left->resolve($scope) < $this->right->resolve($scope);
      case '>': return $this->left->resolve($scope) > $this->right->resolve($scope);
      case '<=': return $this->left->resolve($scope) <= $this->right->resolve($scope);
      case '>=': return $this->left->resolve($scope) >= $this->right->resolve($scope);
      case '<=>': return $this->left->resolve($scope) <=> $this->right->resolve($scope);
      case '===': return $this->left->resolve($scope) === $this->right->resolve($scope);
      case '!==': return $this->left->resolve($scope) !== $this->right->resolve($scope);
    }
  }
}
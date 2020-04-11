<?php namespace lang\ast;

class ParseTree {
  private $children= [];

  public function __construct($children) {
    foreach ($children as $node) {
      $this->children[]= $node;
    }
  }

  private function criteria($path) {
    $r= [];
    foreach (explode('/', ltrim($path, '/')) as $criteria) {
      if ('*' === $criteria) {
        $r[]= function($node) { return true; };
      } else if (false !== strpos($criteria, '.')) {
        $r[]= function($node) use($criteria) { return is($criteria, $node); };
      } else if (false !== strpos($criteria, '|')) {
        $all= [];
        foreach (explode('|', $criteria) as $kind) {
          $all[$kind]= true;
        }
        $r[]= function($node) use($all) { return isset($all[$node->kind]); };
      } else {
        $r[]= function($node) use($criteria) { return $node->is($criteria); };
      }
    }
    return $r;
  }

  private function match($node, $criteria, $i= 0) {
    if ($criteria[$i]($node)) {
      if ($i < sizeof($criteria) - 1) {
        $i++;
        foreach ($node->children() as $child) {
          yield from $this->match($child, $criteria, $i);
        }
      } else {
        yield $node;
      }
    }
  }

  /** @return lang.ast.Node[] */
  public function children() { return $this->children; }

  /**
   * Query for a given path
   *
   * @param  string $path
   * @param  lang.ast.Node $context
   * @return iterable
   */
  public function query($path, Node $context= null) {
    $criteria= $this->criteria($path);
    foreach ($context ? $context->children() : $this->children as $node) {
      foreach ($this->match($node, $criteria) as $found) {
        yield $found;
      }
    }
  }

  /**
   * Collect nodes in a result. Iterates over result exactly once!
   *
   * @param  var $result
   * @param  [:function(var, lang.ast.Node): void] $collectors
   * @return var
   */
  public function collect($result, $collectors, Node $context= null) {
    foreach ($context ? $context->children() : $this->children as $node) {
      foreach ($collectors as $path => $collector) {
        foreach ($this->match($node, $this->criteria($path)) as $found) {
          $collector($result, $found);
        }
      }
    }
    return $result;
  }
}
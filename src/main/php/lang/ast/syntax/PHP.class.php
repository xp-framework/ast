<?php namespace lang\ast\syntax;

use lang\ast\nodes\{
  ArrayLiteral,
  Annotation,
  Annotations,
  Block,
  Braced,
  BreakStatement,
  CallableExpression,
  CallableNewExpression,
  CaseLabel,
  CastExpression,
  CatchStatement,
  ClassDeclaration,
  ClosureExpression,
  Comment,
  Constant,
  ContinueStatement,
  Directives,
  DoLoop,
  EchoStatement,
  EnumCase,
  EnumDeclaration,
  Expression,
  ForLoop,
  ForeachLoop,
  FunctionDeclaration,
  GotoStatement,
  Hook,
  IfStatement,
  InstanceExpression,
  InstanceOfExpression,
  InterfaceDeclaration,
  InvokeExpression,
  Label,
  LambdaExpression,
  Literal,
  Method,
  NamespaceDeclaration,
  NewClassExpression,
  NewExpression,
  NullSafeInstanceExpression,
  OffsetExpression,
  Parameter,
  Property,
  ReturnStatement,
  ScopeExpression,
  Signature,
  StaticLocals,
  SwitchStatement,
  TernaryExpression,
  MatchExpression,
  MatchCondition,
  ThrowExpression,
  ThrowStatement,
  TraitDeclaration,
  TryStatement,
  UnpackExpression,
  UseExpression,
  UseStatement,
  Variable,
  WhileLoop,
  YieldExpression,
  YieldFromExpression
};
use lang\ast\types\{
  IsArray,
  IsExpression,
  IsFunction,
  IsGeneric,
  IsIntersection,
  IsLiteral,
  IsMap,
  IsNullable,
  IsUnion,
  IsValue
};
use lang\ast\{Token, Language, Error};

/**
 * PHP language
 *
 * @see   https://github.com/php/php-langspec
 */
class PHP extends Language {
  private $body= [];

  /** Setup language parser */
  public function __construct() {
    $this->symbol('as');

    $this->constant('true', 'true');
    $this->constant('false', 'false');
    $this->constant('null', 'null');

    $this->infixr('??', 30);
    $this->infixr('?:', 30);
    $this->infixr('&&', 30);
    $this->infixr('||', 30);

    $this->infixr('==', 40);
    $this->infixr('===', 40);
    $this->infixr('!=', 40);
    $this->infixr('!==', 40);
    $this->infixr('<', 40);
    $this->infixr('<=', 40);
    $this->infixr('>', 40);
    $this->infixr('>=', 40);
    $this->infixr('<=>', 40);

    $this->infix('+', 50);
    $this->infix('-', 50);
    $this->infix('&', 50);
    $this->infix('|', 50);
    $this->infix('^', 50);
    $this->suffix('++', 50);
    $this->suffix('--', 50);

    $this->infix('*', 60);
    $this->infix('/', 60);
    $this->infix('%', 60);
    $this->infix('.', 60);
    $this->infix('**', 60);

    $this->infixr('<<', 70);
    $this->infixr('>>', 70);

    $this->infix('instanceof', 110, function($parse, $token, $left) {
      $type= $this->expression($parse, 110);
      if ($type instanceof Literal) {
        return new InstanceOfExpression($left, $parse->scope->resolve($type->expression), $token->line);
      } else {
        return new InstanceOfExpression($left, $type, $token->line);
      }
    });

    $this->infix('->', 100, function($parse, $token, $left) {
      return new InstanceExpression($left, $this->member($parse), $token->line);
    });

    $this->infix('?->', 100, function($parse, $node, $left) {
      $value= new InstanceExpression($left, $this->member($parse), $node->line);
      $value->kind= 'nullsafeinstance';
      return $value;
    });

    $this->infix('::', 120, function($parse, $token, $left) {
      $scope= $left instanceof Literal ? $parse->scope->resolve($left->expression) : $left;
      $expr= $this->member($parse);

      if ('(' === $parse->token->value) {
        $parse->expecting('(', 'invoke expression');

        // Resolve ambiguity by looking ahead: `func(...)` which is a first-class
        // callable reference vs. `func(...$it)` - a call with an unpacked argument
        if ('...' === $parse->token->value) {
          $dots= $parse->token;
          $parse->forward();
          if (')' === $parse->token->value) {
            $parse->forward();
            return new CallableExpression(new ScopeExpression($scope, $expr, $token->line), $token->line);
          }

          array_unshift($parse->queue, $parse->token);
          $parse->token= $dots;
        }

        $arguments= $this->arguments($parse);
        $parse->expecting(')', 'invoke expression');
        $expr= new InvokeExpression($expr, $arguments, $token->line);
      }

      return new ScopeExpression($scope, $expr, $token->line);
    });

    $this->infix('(', 100, function($parse, $token, $left) {

      // Resolve ambiguity by looking ahead: `func(...)` which is a first-class
      // callable reference vs. `func(...$it)` - a call with an unpacked argument
      if ('...' === $parse->token->value) {
        $dots= $parse->token;
        $parse->forward();
        if (')' === $parse->token->value) {
          $parse->forward();
          return new CallableExpression($left, $token->line);
        }

        array_unshift($parse->queue, $parse->token);
        $parse->token= $dots;
      }

      $arguments= $this->arguments($parse);
      $parse->expecting(')', 'invoke expression');
      return new InvokeExpression($left, $arguments, $token->line);
    });

    $this->infix('[', 100, function($parse, $token, $left) {
      if (']' === $parse->token->value) {
        $expr= null;
        $parse->forward();
      } else {
        $expr= $this->expression($parse, 0);
        $parse->expecting(']', 'offset access');
      }

      return new OffsetExpression($left, $expr, $token->line);
    });

    $this->infix('?', 80, function($parse, $token, $left) {
      $when= $this->expression($parse, 0);
      $parse->expecting(':', 'ternary');
      $else= $this->expression($parse, 0);
      return new TernaryExpression($left, $when, $else, $token->line);
    });

    $this->prefix('@', 90);
    $this->prefix('&', 90);
    $this->prefix('!', 90);
    $this->prefix('~', 90);
    $this->prefix('+', 90);
    $this->prefix('-', 90);
    $this->prefix('++', 90);
    $this->prefix('--', 90);
    $this->prefix('clone', 90);

    $this->assignment('=');
    $this->assignment('&=');
    $this->assignment('|=');
    $this->assignment('^=');
    $this->assignment('+=');
    $this->assignment('-=');
    $this->assignment('*=');
    $this->assignment('/=');
    $this->assignment('.=');
    $this->assignment('**=');
    $this->assignment('>>=');
    $this->assignment('<<=');
    $this->assignment('??=');

    // This is ambiguous:
    //
    // - An expression surrounded by parentheses `($a ?? $b)->invoke()`;
    // - A cast `(int)$a` or `(int)($a / 2)`.
    //
    // Resolve by looking ahead after the closing ")"
    $this->prefix('(', 0, function($parse, $token) {
      static $types= ['<' => 1, '>' => 1, '<<' => 1, '>>' => 1, ',' => 1, '?' => 1, '<?' => 1, ':' => 1, '|' => 1, '&' => 1];

      $skipped= [$token, $parse->token];
      $cast= true;
      $level= 1;
      while ($level > 0) {
        if ('(' === $parse->token->value) {
          $level++;
        } else if (')' === $parse->token->value) {
          $level--;
        } else if ('name' !== $parse->token->kind && !isset($types[$parse->token->value])) {
          $cast= false;
        }
        $parse->forward();
        $skipped[]= $parse->token;
      }
      $parse->queue= $parse->queue ? array_merge($skipped, $parse->queue) : $skipped;

      if ($cast && ('operator' !== $parse->token->kind || '(' === $parse->token->value || '[' === $parse->token->value)) {
        $parse->forward();
        $parse->expecting('(', 'cast');
        $type= $this->type($parse, false);
        $parse->expecting(')', 'cast');

        return new CastExpression($type, $this->expression($parse, 0), $token->line);
      } else {
        $parse->forward();
        $parse->expecting('(', 'braced');
        $expr= $this->expression($parse, 0);
        $parse->expecting(')', 'braced');

        return new Braced($expr, $token->line);
      }
    });

    $this->prefix('{', 0, function($parse, $token) {
      $statements= $this->statements($parse);
      $parse->expecting('}', 'block');
      return new Block($statements, $token->line);
    });

    $this->prefix('[', 0, function($parse, $token) {
      return new ArrayLiteral($this->list($parse, ']', 'array literal'), $token->line);
    });

    $this->prefix('list', 0, function($parse, $token) {
      $parse->expecting('(', 'list assignment');
      return new ArrayLiteral($this->list($parse, ')', 'list assignment'), $token->line);
    });

    $this->prefix('new', 0, function($parse, $token) {
      if ('(' === $parse->token->value) {
        $parse->expecting('(', 'new type');
        $type= new IsExpression($this->expression($parse, 0));
        $parse->expecting(')', 'new type');
      } else if ('variable' === $parse->token->kind) {
        $parse->forward();
        $type= new IsExpression($this->variable($parse, 1000));
      } else if ('class' === $parse->token->value) {
        $annotations= null;
        $type= null;
        $parse->forward();
      } else if ('#[' === $parse->token->value) {
        $parse->forward();
        $annotations= $this->annotations($parse, 'anonymous class annotations');
        $parse->expecting('class', 'anonymous class annotations');
        $type= null;
      } else {
        if (null === ($type= $this->type($parse, false))) return $parse->token;
      }

      $parse->expecting('(', 'new arguments');

      // Resolve ambiguity by looking ahead: `new T(...)` which is a first-class
      // callable reference vs. `new T(...$it)` - a call with an unpacked argument
      if ('...' === $parse->token->value) {
        $dots= $parse->token;
        $parse->forward();
        if (')' === $parse->token->value) {
          $parse->forward();

          if (null === $type) {
            $class= $this->class($parse, null);
            $class->annotations= $annotations;
            $new= new NewClassExpression($class, null, $token->line);
          } else {
            $new= new NewExpression($type, null, $token->line);
          }

          return new CallableNewExpression($new, $token->line);
        }

        array_unshift($parse->queue, $parse->token);
        $parse->token= $dots;
      }

      $arguments= $this->arguments($parse);
      $parse->expecting(')', 'new arguments');

      if (null === $type) {
        $class= $this->class($parse, null);
        $class->annotations= $annotations;
        return new NewClassExpression($class, $arguments, $token->line);
      } else {
        return new NewExpression($type, $arguments, $token->line);
      }
    });

    $this->prefix('yield', 0, function($parse, $token) {
      if ('from' === $parse->token->value) {
        $parse->forward();
        return new YieldFromExpression($this->expression($parse, 0), $token->line);
      } else {
        $expr= $this->expression($parse, 0);
        if ('operator' === $expr->kind) {
          array_unshift($parse->queue, $parse->token);
          $parse->token= $expr;
          return new YieldExpression(null, null, $token->line);
        } else if ('=>' === $parse->token->value) {
          $parse->forward();
          return new YieldExpression($expr, $this->expression($parse, 0), $token->line);
        } else {
          return new YieldExpression(null, $expr, $token->line);
        }
      }
    });

    $this->prefix('...', 0, function($parse, $token) {
      return new UnpackExpression($this->expression($parse, 0), $token->line);
    });

    $this->prefix('fn', 0, function($parse, $token) {
      $signature= $this->signature($parse);
      $parse->expecting('=>', 'fn');

      return new LambdaExpression($signature, $this->expression($parse, 0), false, $token->line);
    });

    $this->prefix('function', 0, function($parse, $token) {
      return $this->closure($parse, false);
    });

    $this->prefix('static', 0, function($parse, $token) {
      if ('variable' === $parse->token->kind) {
        $init= [];
        while (';' !== $parse->token->value) {
          $parse->forward();
          $variable= $parse->token->value;
          $parse->forward();

          if ('=' === $parse->token->value) {
            $parse->forward();
            $init[$variable]= $this->expression($parse, 0);
          } else {
            $init[$variable]= null;
          }

          if (',' === $parse->token->value) {
            $parse->forward();
          }
        }
        return new StaticLocals($init, $token->line);
      } else if ('function' === $parse->token->value) {
        $parse->forward();
        return $this->closure($parse, true);
      } else if ('fn' === $parse->token->value) {
        $parse->forward();
        $signature= $this->signature($parse);
        $parse->expecting('=>', 'fn');
        return new LambdaExpression($signature, $this->expression($parse, 0), true, $token->line);
      } else {
        return new Literal($token->value, $token->line);
      }
    });

    $this->prefix('goto', 0, function($parse, $token) {
      $label= $parse->token->value;
      $parse->forward();
      return new GotoStatement($label, $token->line);
    });

    $this->prefix('match', 0, function($parse, $token) {
      if ('(' === $parse->token->value) {
        $parse->forward();
        $condition= $this->expression($parse, 0);
        $parse->expecting(')', 'match');
      } else {
        $condition= null;
      }

      $default= null;
      $cases= [];
      $parse->expecting('{', 'match');
      while ('}' !== $parse->token->value) {
        if ('default' === $parse->token->value) {
          $parse->forward();
          $parse->expecting('=>', 'match');
          $default= $this->expression($parse, 0);
        } else {
          $match= [];
          do {
            $match[]= $this->expression($parse, 0);
          } while (',' === $parse->token->value && $parse->forward() | true);
          $parse->expecting('=>', 'match');
          $cases[]= new MatchCondition($match, $this->expression($parse, 0), $parse->token->line);
        }

        if (',' === $parse->token->value) {
          $parse->forward();
        }
      }
      $parse->expecting('}', 'match');

      return new MatchExpression($condition, $cases, $default, $token->line);
    });

    $this->prefix('throw', 0, function($parse, $token) {
      return new ThrowExpression($this->expression($parse, 0), $token->line);
    });

    $this->prefix('(variable)', 0, function($parse, $token) {
      return $this->variable($parse, 0);
    });

    $this->prefix('(literal)', 0, function($parse, $token) {
      return new Literal($token->value, $token->line);
    });

    $this->prefix('(name)', 0, function($parse, $token) {
      return new Literal($token->value, $token->line);
    });

    $this->stmt(';', function($parse, $token) { return null; });

    // Unexpected standalone symbols, warn but continue
    $unexpected= function($parse, $token) { $parse->raise('Unexpected '.$token->value); };
    $this->stmt('}', $unexpected);
    $this->stmt(']', $unexpected);
    $this->stmt(')', $unexpected);
    $this->stmt(':', $unexpected);
    $this->stmt(',', $unexpected);

    // Unexpected EOF; stop parsing immediately!
    $this->stmt('(end)', function($parse, $token) {
      throw new Error('Unexpected (end)', $parse->file, $token->line);
    });

    $this->stmt('(name)', function($parse, $token) {

      // Solve ambiguity between goto-labels and other statements
      if (':' === $parse->token->value) {
        $node= new Label($token->value, $token->line);
      } else {
        $parse->queue[]= $parse->token;
        $parse->token= $token;
        $node= $this->expression($parse, 0);
      }
      $parse->forward();
      return $node;
    });

    $this->stmt('<?', function($parse, $token) {
      $syntax= $parse->token->value;
      if ('php' !== $syntax) {
        $parse->raise('Unexpected syntax '.$syntax.', expecting php', $token->value);
      }

      $parse->forward();
      return $this->statement($parse);
    });

    $this->stmt('declare', function($parse, $token) {
      $parse->expecting('(', 'declare');

      $declare= [];
      while (')' !== $parse->token->value) {
        $name= $parse->token->value;
        $parse->forward();
        $parse->expecting('=', 'declare');
        $declare[$name]= $this->expression($parse, 0);

        if (')' === $parse->token->value) break;
        $parse->expecting(',', 'declare');        
      }

      $parse->expecting(')', 'declare');
      return new Directives($declare, $token->line);
    });

    $this->stmt('{', function($parse, $token) {
      $statements= $this->statements($parse);
      $parse->expecting('}', 'block');
      return new Block($statements, $token->line);
    });

    $this->prefix('echo', 0, function($parse, $token) {
      return new EchoStatement($this->expressions($parse, ';'), $token->line);
    });

    $this->stmt('namespace', function($parse, $token) {
      $name= $parse->token->value;
      $parse->forward();
      $parse->expecting(';', 'namespace');
      $parse->scope->package($name);
      return new NamespaceDeclaration($name, $token->line);
    });

    $this->stmt('use', function($parse, $token) {
      if ('function' === $parse->token->value) {
        $type= 'function';
        $parse->forward();
      } else if ('const' === $parse->token->value) {
        $type= 'const';
        $parse->forward();
      } else {
        $type= null;  // class, interface or trait
      }

      $names= [];
      do {
        $import= $parse->token->value;
        $parse->forward();

        if ('{' === $parse->token->value) {
          $parse->forward();
          while ('}' !== $parse->token->value) {
            $class= $import.$parse->token->value;

            $parse->forward();
            if ('as' === $parse->token->value) {
              $parse->forward();
              $names[$class]= $parse->token->value;
              $parse->scope->import($parse->token->value);
              $parse->forward();
            } else {
              $names[$class]= null;
              $parse->scope->import($class);
            }

            if (',' === $parse->token->value) {
              $parse->forward();
            } else if ('}' === $parse->token->value) {
              break;
            } else {
              $parse->expecting(', or }', 'use');
              break;
            }
          }
          $parse->forward();
        } else if ('as' === $parse->token->value) {
          $parse->forward();
          $names[$import]= $parse->token->value;
          $parse->scope->import($import, $parse->token->value);
          $parse->forward();
        } else {
          $names[$import]= null;
          $parse->scope->import($import);
        }
      } while (',' === $parse->token->value && true | $parse->forward());

      $parse->expecting(';', 'use');
      return new UseStatement($type, $names, $token->line);
    });

    $this->stmt('if', function($parse, $token) {
      $parse->expecting('(', 'if');
      $condition= $this->expression($parse, 0);
      $parse->expecting(')', 'if');
      $when= $this->block($parse);
      if ('else' === $parse->token->value) {
        $parse->forward();
        $otherwise= $this->block($parse);
      } else {
        $otherwise= null;
      }

      return new IfStatement($condition, $when, $otherwise, $token->line);
    });

    $this->stmt('switch', function($parse, $token) {
      $parse->expecting('(', 'switch');
      $condition= $this->expression($parse, 0);
      $parse->expecting(')', 'switch');

      $cases= [];
      $parse->expecting('{', 'switch');
      while ('}' !== $parse->token->value) {
        if ('default' === $parse->token->value) {
          $parse->forward();
          $parse->expecting(':', 'switch');
          $cases[]= new CaseLabel(null, [], $parse->token->line);
        } else if ('case' === $parse->token->value) {
          $parse->forward();

          // Resolve ambiguity by looking ahead: goto labels vs. global constants as case expressions
          if ('name' === $parse->token->kind) {
            $const= $parse->token;
            $parse->forward();
            if (':' === $parse->token->value) {
              $parse->forward();
              $expr= new Literal($const->value, $const->line);
              $cases[]= new CaseLabel($expr, [], $const->line);
              continue;
            }

            $parse->queue[]= $parse->token;
            $parse->token= $const;
          }

          $expr= $this->expression($parse, 0);
          $parse->expecting(':', 'switch');
          $cases[]= new CaseLabel($expr, [], $parse->token->line);
        } else {
          $cases[sizeof($cases) - 1]->body[]= $this->statement($parse);
        }
      }
      $parse->forward();

      return new SwitchStatement($condition, $cases, $token->line);
    });

    $this->stmt('break', function($parse, $token) {
      if (';' === $parse->token->value) {
        $expr= null;
        $parse->forward();
      } else {
        $expr= $this->expression($parse, 0);
        $parse->expecting(';', 'break');
      }

      return new BreakStatement($expr, $token->line);
    });

    $this->stmt('continue', function($parse, $token) {
      if (';' === $parse->token->value) {
        $expr= null;
        $parse->forward();
      } else {
        $expr= $this->expression($parse, 0);
        $parse->expecting(';', 'continue');
      }

      return new ContinueStatement($expr, $token->line);
    });

    $this->stmt('do', function($parse, $token) {
      $block= $this->block($parse);
      $parse->expecting('while', 'do');
      $parse->expecting('(', 'do');
      $expression= $this->expression($parse, 0);
      $parse->expecting(')', 'do');
      $parse->expecting(';', 'do');
      return new DoLoop($expression, $block, $token->line);
    });

    $this->stmt('while', function($parse, $token) {
      $parse->expecting('(', 'while');
      $expression= $this->expression($parse, 0);
      $parse->expecting(')', 'while');
      $block= $this->block($parse);
      return new WhileLoop($expression, $block, $token->line);
    });

    $this->stmt('for', function($parse, $token) {
      $parse->expecting('(', 'for');
      $init= $this->expressions($parse, ';');
      $parse->expecting(';', 'for');
      $cond= $this->expressions($parse, ';');
      $parse->expecting(';', 'for');
      $loop= $this->expressions($parse, ')');
      $parse->expecting(')', 'for');
      $block= $this->block($parse);
      return new ForLoop($init, $cond, $loop, $block, $token->line);
    });

    $this->stmt('foreach', function($parse, $token) {
      $parse->expecting('(', 'foreach');
      $expression= $this->expression($parse, 0);
      $parse->expecting('as', 'foreach');
      $expr= $this->expression($parse, 0);

      if ('=>' === $parse->token->value) {
        $parse->forward();
        $key= $expr;
        $value= $this->expression($parse, 0);
      } else {
        $key= null;
        $value= $expr;
      }

      $parse->expecting(')', 'foreach');
      $block= $this->block($parse);
      return new ForeachLoop($expression, $key, $value, $block, $token->line);
    });

    $this->stmt('throw', function($parse, $token) {
      $expr= $this->expression($parse, 0);
      $parse->expecting(';', 'throw');
      return new ThrowStatement($expr, $token->line);
    });

    $this->stmt('try', function($parse, $token) {
      $parse->expecting('{', 'try');
      $statements= $this->statements($parse);
      $parse->expecting('}', 'try');

      $catches= [];
      while ('catch'  === $parse->token->value) {
        $parse->forward();
        $parse->expecting('(', 'try');

        $types= [];
        while ('name' === $parse->token->kind) {
          $types[]= $parse->scope->resolve($parse->token->value);
          $parse->forward();
          if ('|' !== $parse->token->value) break;
          $parse->forward();
        }

        if (')' === $parse->token->value) {
          $variable= null;
          $parse->forward();
        } else {
          $parse->forward();
          $variable= $parse->token->value;
          $parse->forward();
          $parse->expecting(')', 'catch');
        }

        $parse->expecting('{', 'catch');
        $catches[]= new CatchStatement($types, $variable, $this->statements($parse), $parse->token->line);
        $parse->expecting('}', 'catch');
      }

      if ('finally' === $parse->token->value) {
        $parse->forward();
        $parse->expecting('{', 'finally');
        $finally= $this->statements($parse);
        $parse->expecting('}', 'finally');
      } else {
        $finally= null;
      }

      return new TryStatement($statements, $catches, $finally, $token->line);
    });

    $this->stmt('return', function($parse, $token) {
      if (';' === $parse->token->value) {
        $expr= null;
        $parse->forward();
      } else {
        $expr= $this->expression($parse, 0);
        $parse->expecting(';', 'return');
      }

      return new ReturnStatement($expr, $token->line);
    });

    $this->stmt('abstract', function($parse, $token) {
      $type= $this->statement($parse);
      $type->modifiers[]= 'abstract';
      return $type;
    });

    $this->stmt('final', function($parse, $token) {
      $type= $this->statement($parse);
      $type->modifiers[]= 'final';
      return $type;
    });

    $this->stmt('readonly', function($parse, $token) {
      $type= $this->statement($parse);
      $type->modifiers[]= 'readonly';
      return $type;
    });

    $this->stmt('#[', function($parse, $token) {
      $annotations= $this->annotations($parse, 'annotations');
      return $this->statement($parse)->annotate($annotations);
    });

    $this->stmt('function', function($parse, $token) {
      $name= $parse->token->value;

      // Function expression used as statement (e.g. for pure side-effects!)
      if ('(' === $name) {
        $parse->queue= [$parse->token];
        $parse->token= new Token($this->symbol('function'));
        $parse->token->line= $token->line;
        $expr= $this->expression($parse, 0);

        $parse->queue= [$parse->token];
        $parse->token= new Token($this->symbol(';'));
        return $expr;
      }

      $parse->forward();
      $signature= $this->signature($parse);
      $parse->expecting('{', 'function');
      $statements= $this->statements($parse);
      $parse->expecting('}', 'function');

      return new FunctionDeclaration($name, $signature, $statements, $token->line);
    });

    $this->stmt('class', function($parse, $token) {
      $comment= $parse->comment;
      $parse->comment= null;
      $name= $this->type($parse, false);

      return $this->class($parse, $name, $comment);
    });

    $this->stmt('interface', function($parse, $token) {
      $comment= $parse->comment;
      $parse->comment= null;
      $name= $this->type($parse, false);

      $parents= [];
      if ('extends' === $parse->token->value) {
        $parse->forward();
        do {
          $parents[]= $this->type($parse, false);
          if (',' === $parse->token->value) {
            $parse->forward();
          } else if ('{' === $parse->token->value) {
            break;
          } else {
            $parse->expecting(', or {', 'interface parents');
          }
        } while (true);
      }

      $decl= new InterfaceDeclaration([], $name, $parents, [], null, $comment, $token->line);
      $parse->expecting('{', 'interface');
      $decl->body= $this->typeBody($parse, $decl->name);
      $parse->expecting('}', 'interface');

      return $decl;
    });

    $this->stmt('trait', function($parse, $token) {
      $comment= $parse->comment;
      $parse->comment= null;
      $name= $this->type($parse, false);

      $decl= new TraitDeclaration([], $name, [], null, $comment, $token->line);
      $parse->expecting('{', 'trait');
      $decl->body= $this->typeBody($parse, $decl->name);
      $parse->expecting('}', 'trait');

      return $decl;
    });

    $this->stmt('enum', function($parse, $token) {
      $comment= $parse->comment;
      $parse->comment= null;
      $name= $this->type($parse, false);

      $implements= [];
      if ('implements' === $parse->token->value) {
        $parse->forward();
        do {
          $implements[]= $this->type($parse, false);
          if (',' === $parse->token->value) {
            $parse->forward();
            continue;
          } else if ('{' === $parse->token->value) {
            break;
          } else {
            $parse->expecting(', or {', 'interfaces list');
          }
        } while (null !== $parse->token->value);
      }

      // Backed enums vs. unit enums
      if (':' === $parse->token->value) {
        $parse->forward();
        $base= $parse->token->value;
        $parse->forward();
      } else {
        $base= null;
      }

      $decl= new EnumDeclaration([], $name, $base, $implements, [], null, $comment, $token->line);
      $parse->expecting('{', 'enum');
      $decl->body= $this->typeBody($parse, $decl->name);
      $parse->expecting('}', 'enum');

      return $decl;
    });

    $this->body('case', function($parse, &$body, $meta, $modifiers, $holder) {
      $comment= $parse->comment;
      $parse->comment= null;

      $parse->forward();
      do {
        $line= $parse->token->line;
        $name= $parse->token->value;

        $parse->forward();
        if ('=' === $parse->token->value) {
          $parse->forward();
          $expr= $this->expression($parse, 0);
        } else {
          $expr= null;
        }

        $body[$name]= new EnumCase($name, $expr, $meta[DETAIL_ANNOTATIONS] ?? null, $comment, $line, $holder);
      } while (',' === $parse->token->value && true | $parse->forward());

      $parse->expecting(';', 'case');
    });

    $this->body('use', function($parse, &$body, $meta, $modifiers, $holder) {
      $line= $parse->token->line;

      $parse->forward();
      $types= [];
      do {
        $types[]= $parse->scope->resolve($parse->token->value);
        $parse->forward();
      } while (',' === $parse->token->value && true | $parse->forward());

      $aliases= [];
      if ('{' === $parse->token->value) {
        $parse->forward();
        while ('}' !== $parse->token->value) {
          $method= $parse->token->value;
          $parse->forward();
          if ('::' === $parse->token->value) {
            $parse->forward();
            $method= $parse->scope->resolve($method).'::'.$parse->token->value;
            $parse->forward();
          }

          if ('as' === $parse->token->value ) {
            $parse->forward();
            $aliases[$method]= ['as' => $parse->token->value];
          } else if ('insteadof' === $parse->token->value) {
            $parse->forward();
            $aliases[$method]= ['insteadof' => $parse->scope->resolve($parse->token->value)];
          } else {
            $parse->expecting('as or insteadof', 'use');
          }
          $parse->forward();
          $parse->expecting(';', 'use');
        }
        $parse->expecting('}', 'use');
      } else {
        $parse->expecting(';', 'use');
      }

      $body[]= new UseExpression($types, $aliases, $line);
    });

    $this->body('const', function($parse, &$body, $meta, $modifiers, $holder) {
      $comment= $parse->comment;
      $parse->comment= null;
      $parse->forward();

      $type= null;
      while (';' !== $parse->token->value) {
        $line= $parse->token->line;
        $first= $parse->token;
        $parse->forward();

        // Untyped `const T = 5` vs. typed `const int T = 5`
        if ('=' === $parse->token->value) {
          $name= $first->value;
        } else {
          $parse->queue[]= $first;
          $parse->queue[]= $parse->token;
          $parse->token= $first;

          $type= $this->type($parse, false);
          $parse->forward();
          $name= $parse->token->value;
          $parse->forward();
        }

        if (isset($body[$name])) {
          $parse->raise('Cannot redeclare constant '.$name);
        }

        $parse->expecting('=', 'const');
        $body[$name]= new Constant(
          $modifiers,
          $name,
          $type,
          $this->expression($parse, 0),
          $meta[DETAIL_ANNOTATIONS] ?? null,
          $comment,
          $line,
          $holder
        );
        if (',' === $parse->token->value) {
          $parse->forward();
        }
      }
      $parse->expecting(';', 'constant declaration');
    });

    $this->body('$', function($parse, &$body, $meta, $modifiers, $holder) {
      $this->properties($parse, $body, $meta, $modifiers, null, $holder);
    });

    $this->body('function', function($parse, &$body, $meta, $modifiers, $holder) {
      $comment= $parse->comment;
      $parse->comment= null;
      $line= $parse->token->line;

      $parse->forward();
      $name= $parse->token->value;
      $lookup= $name.'()';
      if (isset($body[$lookup])) {
        $parse->raise('Cannot redeclare method '.$lookup);
      }

      $parse->forward();
      $signature= $this->signature($parse);

      if ('{' === $parse->token->value) {          // Regular body
        $parse->forward();
        $statements= $this->statements($parse);
        $parse->expecting('}', 'method declaration');
      } else if (';' === $parse->token->value) {   // Abstract or interface method
        $statements= null;
        $parse->expecting(';', 'method declaration');
      } else {
        $parse->expecting('{ or ;', 'method declaration');
        return;
      }

      $body[$lookup]= new Method(
        $modifiers,
        $name,
        $signature,
        $statements,
        $meta[DETAIL_ANNOTATIONS] ?? null,
        $comment,
        $line,
        $holder
      );
    });
  }

  public function list($parse, $end, $kind) {
    $values= [];
    while ($end !== $parse->token->value) {
      if (',' === $parse->token->value) {
        $values[]= [null, null];
      } else {
        $expr= $this->expression($parse, 0);

        if ('=>' === $parse->token->value) {
          $parse->forward();
          $values[]= [$expr, $this->expression($parse, 0)];
        } else {
          $values[]= [null, $expr];
        }
      }

      if ($end === $parse->token->value) break;
      $parse->expecting(',', $kind);
    }

    $parse->expecting($end, $kind);
    return $values;
  }

  public function type($parse, $optional= true) {
    $t= $this->type0($parse, $optional);

    // Check for union and intersection types (which cannot be mixed
    // with one another!).
    if ('|' === $parse->token->value) {
      $t= new IsUnion([$t]);
      do {
        $parse->forward();
        $t->components[]= $this->type0($parse, false);
      } while ('|' === $parse->token->value);
    } else if ('&' === $parse->token->value) {
      $i= null;
      do {
        $token= $parse->token;
        $parse->forward();

        // Solve ambiguity `T1&T2` vs. `T1 &$param`
        if ('variable' === $parse->token->kind) {
          array_unshift($parse->queue, $parse->token);
          $parse->token= $token;
          return $t;
        }

        $i ?? $i= $t= new IsIntersection([$t]);
        $t->components[]= $this->type0($parse, false);
      } while ('&' === $parse->token->value);
    }

    return $t;
  }

  private function variable($parse, $rbp) {
    $line= $parse->token->line;

    // Constant vs. dynamic variables: $name, $$name, ${<expr>}
    if ('name' === $parse->token->kind) {
      $ptr= $parse->token->value;
      $parse->forward();
    } else if ('variable' === $parse->token->kind) {
      $ptr= $this->expression($parse, $rbp);
    } else {
      $parse->expecting('{', 'variable expression');
      $expr= $this->expression($parse, 0);
      $parse->expecting('}', 'variable expression');
      $ptr= new Expression($expr, $line);
    }

    return new Variable($ptr, $line);
  }

  private function member($parse) {
    if ('{' === $parse->token->value) {
      $line= $parse->token->line;
      $parse->forward();
      $expr= new Expression($this->expression($parse, 0), $line);
      $parse->expecting('}', 'dynamic member');
    } else if ('variable' === $parse->token->kind) {
      $parse->forward();
      $expr= $this->variable($parse, 0);
    } else if ('name' === $parse->token->kind) {
      $expr= new Literal($parse->token->value, $parse->token->line);
      $parse->forward();
    } else {
      $parse->expecting('an expression in curly braces, a name or a variable', 'member');
      $expr= new Literal($parse->token->value, $parse->token->line);
      $parse->forward();
    }
    return $expr;
  }

  private function type0($parse, $optional) {
    static $literal= [
      'string'   => true,
      'int'      => true,
      'float'    => true,
      'bool'     => true,
      'mixed'    => true,
      'array'    => true,
      'void'     => true,
      'never'    => true,
      'resource' => true,
      'callable' => true,
      'iterable' => true,
      'object'   => true,
      'null'     => true,
      'false'    => true,
      'true'     => true,
      'double'   => true,  // BC
    ];

    if ('?' === $parse->token->value) {
      $parse->forward();
      return new IsNullable($this->type($parse, false));
    } else if ('(' === $parse->token->value) {
      $parse->forward();
      $type= $this->type($parse, false);
      $parse->forward();
      return $type;
    } else if ('name' === $parse->token->kind && 'function' === $parse->token->value) {
      $parse->forward();
      $parse->expecting('(', 'type');
      $signature= [];
      if (')' !== $parse->token->value) do {
        $signature[]= $this->type($parse, false);
      } while (',' === $parse->token->value && true | $parse->forward());
      $parse->expecting(')', 'type');
      $parse->expecting(':', 'type');
      return new IsFunction($signature, $this->type($parse, false));
    } else if ('name' === $parse->token->kind) {
      $type= $parse->scope->resolve($parse->token->value);
      $parse->forward();
    } else if ($optional) {
      return null;
    } else {
      $parse->expecting('type name', 'type');
      return null;
    }

    // Resolve ambiguity between short open tag and nullables as in <?int>
    if ('<?' === $parse->token->value) {
      array_unshift($parse->queue, new Token(self::symbol('?'), '(operator)', '?'));
      $parse->token->value= '<';
    }

    if ('<' === $parse->token->value) {
      $parse->forward();
      $components= [];
      do {

        // Resolve ambiguity between generic placeholders and nullable
        if ('?' === $parse->token->value) {
          $parse->forward();
          if (',' === $parse->token->value || '>' === $parse->token->value) {
            $components[]= new IsLiteral('?');
          } else {
            $components[]= new IsNullable($this->type($parse, false));
          }
        } else {
          $components[]= $this->type($parse, false);
        }

        if ('>' === $parse->token->symbol->id) {
          break;
        } else if ('>>' === $parse->token->value) {
          array_unshift($parse->queue, $parse->token= new Token(self::symbol('>')));
          break;
        }
      } while (',' === $parse->token->value && true | $parse->forward());
      $parse->expecting('>', 'type');

      if ('array' === $type) {
        return 1 === sizeof($components) ? new IsArray($components[0]) : new IsMap($components[0], $components[1]);
      } else {
        return new IsGeneric(new IsValue($type), $components);
      }
    }

    return isset($literal[$type]) ? new IsLiteral($type) : new IsValue($type);
  }

  private function properties($parse, &$body, $meta, $modifiers, $type, $holder) {
    $comment= $parse->comment;
    $parse->comment= null;
    $annotations= $meta[DETAIL_ANNOTATIONS] ?? null;

    do {
      $line= $parse->token->line;

      // Untyped `$a` vs. typed `int $a`
      if ('variable' !== $parse->token->kind) {
        $type= $this->type($parse, false);
      }
      $parse->forward();
      $name= $parse->token->value;

      $lookup= '$'.$name;
      if (isset($body[$lookup])) {
        $parse->raise('Cannot redeclare property '.$lookup);
      }

      $parse->forward();
      if ('=' === $parse->token->value) {
        $parse->forward();
        $expr= $this->expression($parse, 0);
      } else {
        $expr= null;
      }

      $body[$lookup]= new Property($modifiers, $name, $type, $expr, $annotations, $comment, $line, $holder);

      // Check for property hooks
      if (';' === $parse->token->value) {
        $parse->forward();
        return;
      } else if (',' === $parse->token->value) {
        $parse->forward();
        continue;
      } else if ('=>' === $parse->token->value) {
        $parse->forward();
        $expr= $this->expression($parse, 0);
        $parse->expecting(';', 'field hook');

        $body[$lookup]->hooks['get']= new Hook([], 'get', $name, $expr, null, $line, $holder);
        return;
      } else if ('{' === $parse->token->value) {
        $parse->forward();

        while ('}' !== $parse->token->value) {
          if ('final' === $parse->token->value) {
            $modifiers= ['final'];
            $parse->forward();
          } else {
            $modifiers= [];
          }

          $hook= $parse->token->value;
          $parse->forward();

          if ('(' === $parse->token->value) {
            $parse->forward();

            if ('#[' === $parse->token->value) {
              $parse->forward();
              $annotations= $this->annotations($parse, 'parameter annotations');
            } else {
              $annotations= null;
            }

            $line= $parse->token->line;
            $type= $this->type($parse);

            if ('...' === $parse->token->value) {
              $variadic= true;
              $parse->forward();
            } else {
              $variadic= false;
            }

            if ('&' === $parse->token->value) {
              $byref= true;
              $parse->forward();
            } else {
              $byref= false;
            }

            $parse->forward();
            $param= $parse->token->value;
            $parse->forward();

            if ('=' === $parse->token->value) {
              $parse->forward();
              $default= $this->expression($parse, 0);
            } else {
              $default= null;
            }

            $parameter= new Parameter($param, $type, $default, $byref, $variadic, null, $annotations, null, $line);
            $parse->expecting(')', 'field hook');
          } else {
            $parameter= null;
          }

          $line= $parse->token->line;
          if ('=>' === $parse->token->value) {
            $parse->forward();
            $expr= $this->expression($parse, 0);
            $parse->expecting(';', 'field hook');
          } else if ('{' === $parse->token->value) {
            $parse->forward();
            $expr= new Block($this->statements($parse), $line);
            $parse->expecting('}', 'field hook');
          }

          $body[$lookup]->hooks[$hook]= new Hook($modifiers, $hook, $name, $expr, $parameter, $line, $holder);
        }

        $parse->forward();
        return;
      } else {
        $parse->expecting(';, , or {', 'field');
      }
    } while (null !== $parse->token->value);
  }

  /** Parses PHP 8 attributes (#[Test]) */
  private function annotations($parse, $context) {
    $annotations= new Annotations([], $parse->token->line);

    do {
      $name= ltrim($parse->scope->resolve($parse->token->value), '\\');
      $parse->forward();

      if ('(' === $parse->token->value) {
        $parse->expecting('(', $context);
        $annotations->add(new Annotation($name, $this->arguments($parse), $parse->token->line));
        $parse->expecting(')', $context);
      } else {
        $annotations->add(new Annotation($name, [], $parse->token->line));
      }
    } while (',' === $parse->token->value && true | $parse->forward());
    $parse->expecting(']', $context);

    return $annotations;
  }

  private function parameters($parse) {
    static $promotion= ['private' => true, 'protected' => true, 'public' => true];

    $parameters= [];
    while (')' !== $parse->token->value) {
      if ('#[' === $parse->token->value) {
        $parse->forward();
        $annotations= $this->annotations($parse, 'parameter annotations');
      } else {
        $annotations= null;
      }

      $line= $parse->token->line;
      if ('name' === $parse->token->kind && isset($promotion[$parse->token->value])) {
        $promote= $parse->token->value;
        $parse->forward();

        // It would be better to use an array for promote, but this way we keep BC
        if ('readonly' === $parse->token->value) {
          $promote.= ' readonly';
          $parse->forward();
        }
      } else {
        $promote= null;
      }

      $type= $this->type($parse);

      if ('...' !== $parse->token->value) {
        $variadic= false;
      } else if ($promote) {
        $parse->raise('Variadic parameters cannot be promoted', 'parameters');
        $variadic= true;
        $parse->forward();
      } else {
        $variadic= true;
        $parse->forward();
      }

      if ('&' === $parse->token->value) {
        $byref= true;
        $parse->forward();
      } else {
        $byref= false;
      }

      $parse->forward();
      $name= $parse->token->value;
      $parse->forward();

      $default= null;
      if ('=' === $parse->token->value) {
        $parse->forward();
        $default= $this->expression($parse, 0);
      }
      $parameters[]= new Parameter($name, $type, $default, $byref, $variadic, $promote, $annotations, null, $line);

      if (')' === $parse->token->value) {
        break;
      } else if (',' === $parse->token->value) {
        $parse->forward();
        continue;
      } else {
        $parse->expecting(',', 'parameter list');
        break;
      }
    }
    return $parameters;
  }

  public function body($id, $func) {
    $this->body[$id]= $func->bindTo($this, static::class);
  }

  public function typeBody($parse, $holder) {
    static $modifier= [
      'private'   => true,
      'protected' => true,
      'public'    => true,
      'static'    => true,
      'final'     => true,
      'abstract'  => true,
      'readonly'  => true,
    ];

    $body= [];
    $modifiers= [];
    $meta= [];
    while ('}' !== $parse->token->value) {
      if (isset($modifier[$parse->token->value])) {
        $modifiers[]= $parse->token->value;
        $parse->forward();
      } else if ($f= $this->body[$parse->token->value] ?? $this->body['@'.$parse->token->kind] ?? null) {
        $f($parse, $body, $meta, $modifiers, $holder);
        $modifiers= [];
        $meta= [];
      } else if ('#[' === $parse->token->value) {
        $parse->forward();
        $meta[DETAIL_ANNOTATIONS]= $this->annotations($parse, 'member annotations');
      } else if ($type= $this->type($parse)) {
        $this->properties($parse, $body, $meta, $modifiers, $type, $holder);
        $modifiers= [];
        $meta= [];
      } else {
        $parse->raise(sprintf(
          'Expected a type, modifier, property, annotation, method or "}", have "%s"',
          $parse->token->symbol->id
        ));
        $parse->forward();
      }
    }
    return $body;
  }

  public function signature($parse) {
    $line= $parse->token->line;
    $parse->expecting('(', 'signature');
    $parameters= $this->parameters($parse);
    $parse->expecting(')', 'signature');

    if (':' === $parse->token->value) {
      $parse->forward();
      $return= $this->type($parse);
    } else {
      $return= null;
    }

    return new Signature($parameters, $return, null, $line);
  }

  public function closure($parse, $static) {
    $line= $parse->token->line;
    $signature= $this->signature($parse);

    if ('use' === $parse->token->value) {
      $parse->forward();
      $parse->forward();
      $use= [];
      while (')' !== $parse->token->value) {
        if ('&' === $parse->token->value) {
          $parse->forward();
          $parse->forward();
          $use[]= '&$'.$parse->token->value;
        } else {
          $parse->forward();
          $use[]= '$'.$parse->token->value;
        }
        $parse->forward();
        if (')' === $parse->token->value) break;
        $parse->expecting(',', 'use list');
      }
      $parse->expecting(')', 'closure');
    } else {
      $use= null;
    }

    $parse->expecting('{', 'function');
    $statements= $this->statements($parse);
    $parse->expecting('}', 'function');

    return new ClosureExpression($signature, $use, $statements, $static, $line);
  }

  public function block($parse) {
    if ('{'  === $parse->token->value) {
      $parse->forward();
      $block= $this->statements($parse);
      $parse->expecting('}', 'block');
      return $block;
    } else {
      return [$this->statement($parse)];
    }
  }

  public function class($parse, $name, $comment= null, $modifiers= []) {
    $line= $parse->token->line;
    $parent= null;
    if ('extends' === $parse->token->value) {
      $parse->forward();
      $parent= $this->type($parse, false);
    }

    $implements= [];
    if ('implements' === $parse->token->value) {
      $parse->forward();
      do {
        $implements[]= $this->type($parse, false);
        if (',' === $parse->token->value) {
          $parse->forward();
        } else if ('{' === $parse->token->value) {
          break;
        } else {
          $parse->expecting(', or {', 'interfaces list');
        }
      } while (true);
    }

    $decl= new ClassDeclaration($modifiers, $name, $parent, $implements, [], null, $comment, $line);
    $parse->expecting('{', 'class');
    $decl->body= $this->typeBody($parse, $decl->name);
    $parse->expecting('}', 'class');

    return $decl;
  }

  public function arguments($parse) {
    $arguments= [];
    while (')' !== $parse->token->value) {

      // Named arguments (name: <argument>) vs. positional arguments
      if ('name' === $parse->token->kind) {
        $token= $parse->token;
        $parse->forward();
        if (':' === $parse->token->value) {
          $parse->forward();
          $arguments[$token->value]= $this->expression($parse, 0);
        } else {
          array_unshift($parse->queue, $parse->token);
          $parse->token= $token;
          $arguments[]= $this->expression($parse, 0);
        }
      } else {
        $arguments[]= $this->expression($parse, 0);
      }

      if (',' === $parse->token->value) {
        $parse->forward();
      } else if (')' === $parse->token->value) {
        break;
      } else {
        $parse->expecting(') or ,', 'argument list');
        break;
      }
    }
    return $arguments;
  }

  public function expressions($parse, $end) {
    $expressions= [];
    while ($end !== $parse->token->value) {
      $expressions[]= $this->expression($parse, 0);
      if (',' === $parse->token->value) {
        $parse->forward();
      } else if ($end === $parse->token->value) {
        break;
      } else {
        $parse->expecting($end.' or ,', 'argument list');
        break;
      }
    }
    return $expressions;
  }
}
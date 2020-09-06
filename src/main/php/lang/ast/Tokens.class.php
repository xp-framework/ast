<?php namespace lang\ast;

use io\streams\InputStream;
use io\{Path, File};
use lang\FormatException;
use text\{Tokenizer, StringTokenizer, StreamTokenizer};

class Tokens implements \IteratorAggregate {
  const DELIMITERS = " |&^?!.:;,@%~=<>(){}[]#+-*/'\$\"\r\n\t";

  private static $operators= [
    '<' => ['<=', '<<', '<>', '<?', '<=>', '<<='],
    '>' => ['>=', '>>', '>>='],
    '=' => ['=>', '==', '==='],
    '!' => ['!=', '!=='],
    '&' => ['&&', '&='],
    '|' => ['||', '|='],
    '^' => ['^='],
    '+' => ['+=', '++'],
    '-' => ['-=', '--', '->'],
    '*' => ['*=', '**', '**='],
    '/' => ['/='],
    '~' => ['~='],
    '%' => ['%='],
    '?' => ['?:', '??', '?->', '??='],
    '.' => ['.=', '...'],
    ':' => ['::'],
    '@' => ['@@'],
    "\303" => ["\303\227"]
  ]; 

  private $tokens;
  public $source;

  /**
   * Create new iterable tokens from a string or a stream tokenizer
   *
   * @param  text.Tokenizer|io.streams.InputStream|io.File|io.Path|string $tokens
   * @param  ?string $file
   */
  public function __construct($input, $file= null) {
    if ($input instanceof Tokenizer) {
      $this->tokens= $input;
      $this->tokens->delimiters= self::DELIMITERS;
      $this->tokens->returnDelims= true;
      $this->source= $file ?? '(tokens)';
    } else if ($input instanceof InputStream) {
      $this->tokens= new StreamTokenizer($input, self::DELIMITERS, true);
      $this->source= $file ?? '(stream)';
    } else if ($input instanceof Path) {
      $this->tokens= new StreamTokenizer($input->asFile()->in(), self::DELIMITERS, true);
      $this->source= $file ?? $path->toString();
    } else if ($input instanceof File) {
      $this->tokens= new StreamTokenizer($input->in(), self::DELIMITERS, true);
      $this->source= $file ?? $input->getURI();
    } else {
      $this->tokens= new StringTokenizer($input, self::DELIMITERS, true);
      $this->source= $file ?? '(string)';
    }
  }

  /** @return php.Iterator */
  public function getIterator() {
    $line= 1;
    while (null !== ($token= $this->tokens->nextToken())) {
      if ('$' === $token) {
        yield 'variable' => [$this->tokens->nextToken(), $line];
      } else if ('"' === $token || "'" === $token) {
        $string= $token;
        $end= '\\'.$token;
        do {
          $t= $this->tokens->nextToken($end);
          if (null === $t) {
            throw new FormatException('Unclosed string literal starting at line '.$line);
          } else if ('\\' === $t) {
            $string.= $t.$this->tokens->nextToken($end);
          } else {
            $string.= $t;
          }
        } while ($token !== $t);

        yield 'string' => [$string, $line];
        $line+= substr_count($string, "\n");
      } else if ("\n" === $token) {
        $line++;
      } else if ("\r" === $token || "\t" === $token || ' ' === $token) {
        // Skip
      } else if (0 === strcspn($token, '0123456789')) {
        if ('.' === ($next= $this->tokens->nextToken())) {
          yield 'decimal' => [str_replace('_', '', $token.$next.$this->tokens->nextToken()), $line];
        } else {
          $this->tokens->pushBack($next);
          yield 'integer' => [str_replace('_', '', $token), $line];
        }
      } else if (0 === strcspn($token, self::DELIMITERS)) {
        if ('.' === $token) {
          $next= $this->tokens->nextToken();
          if (0 === strcspn($next, '0123456789')) {
            yield 'decimal' => [".$next", $line];
            continue;
          }
          $this->tokens->pushBack($next);
        } else if ('/' === $token) {
          $next= $this->tokens->nextToken();
          if ('/' === $next) {
            $this->tokens->nextToken("\r\n");
            continue;
          } else if ('*' === $next) {
            $comment= '';
            do {
              $t= $this->tokens->nextToken('/');
              $comment.= $t;
            } while ('*' !== $t[strlen($t)- 1] && $this->tokens->hasMoreTokens());
            $comment.= $this->tokens->nextToken('/');
            yield 'comment' => [trim(preg_replace('/\n\s+\* ?/', "\n", substr($comment, 1, -2))), $line];
            $line+= substr_count($comment, "\n");
            continue;
          }
          $this->tokens->pushBack($next);
        } else if ('#' === $token) {
          $comment= $this->tokens->nextToken("\r\n").$this->tokens->nextToken("\r\n");
          $next= '#';
          do {
            $s= strspn($next, ' ');
            if ('#' !== $next[$s]) break;
            $comment.= substr($next, $s + 1);
            $next= $this->tokens->nextToken("\r\n").$this->tokens->nextToken("\r\n");
          } while ($this->tokens->hasMoreTokens());

          // XP annotations vs. PHP 8 attributes
          if (0 === strncmp($comment, '[@', 2)) {
            $this->tokens->pushBack(substr($comment, 1).$next);
            yield 'operator' => ['#[@', $line];
          } else if ('[' === $comment[0]) {
            $this->tokens->pushBack(substr($comment, 1).$next);
            yield 'operator' => ['#[', $line];
          } else {
            $line+= substr_count($comment, "\n");
            $this->tokens->pushBack($next);
          }
          continue;
        }

        if (isset(self::$operators[$token])) {
          $combined= $token;
          foreach (self::$operators[$token] as $operator) {
            while (strlen($combined) < strlen($operator) && $this->tokens->hasMoreTokens()) {
              $combined.= $this->tokens->nextToken();
            }
            $combined === $operator && $token= $combined;
          }

          $this->tokens->pushBack(substr($combined, strlen($token)));
        }
        yield 'operator' => [$token, $line];
      } else {
        yield 'name' => [$token, $line];
      }
    }
  }
}
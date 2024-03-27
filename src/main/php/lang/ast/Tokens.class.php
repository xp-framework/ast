<?php namespace lang\ast;

use io\streams\{InputStream, MemoryInputStream};
use io\{Path, File};
use lang\FormatException;

/**
 * Tokenizes code.
 *
 * @test  lang.ast.unittest.TokensTest
 */
class Tokens {
  const DELIMITERS = " \r\n\t'\$\"=,;.:?!(){}[]#+-*/|&^@%~<>";
  const OPERATORS = [
    '<' => ['<=>', '<<=', '<=', '<<', '<>', '<?'],
    '>' => ['>>=', '>=', '>>'],
    '=' => ['===', '=>', '=='],
    '!' => ['!==', '!='],
    '&' => ['&&=', '&&', '&='],
    '|' => ['||=', '||', '|=', '|>'],
    '^' => ['^='],
    '+' => ['+=', '++'],
    '-' => ['-=', '--', '->'],
    '*' => ['**=', '*=', '**'],
    '/' => ['/='],
    '~' => ['~='],
    '%' => ['%='],
    '?' => ['?->', '?|>', '??=', '?:', '??'],
    '.' => ['...', '.='],
    ':' => ['::'],
    '[' => [],
    ']' => [],
    '{' => [],
    '}' => [],
    '(' => [],
    ')' => [],
    ',' => [],
    ';' => [],
    '@' => [],
  ]; 

  private $in;
  public $source;

  /**
   * Create new iterable tokens from a string or a stream tokenizer
   *
   * @param  io.streams.InputStream|io.File|io.Path|string $input
   * @param  ?string $file
   */
  public function __construct($input, $file= null) {
    if ($input instanceof InputStream) {
      $this->in= $input;
      $this->source= $file ?? '(stream)';
    } else if ($input instanceof Path) {
      $this->in= $input->asFile()->in();
      $this->source= $file ?? $input->toString();
    } else if ($input instanceof File) {
      $this->in= $input->in();
      $this->source= $file ?? $input->getURI();
    } else {
      $this->in= new MemoryInputStream($input);
      $this->source= $file ?? '(string)';
    }
  }

  /**
   * Creates token iterator
   *
   * @param  lang.ast.Language $language
   * @return iterable
   */
  public function iterator($language) {
    $buffer= '';
    $length= $offset= 0;

    // Read until either delimiters are encountered or EOF
    $next= function($delimiters) use(&$buffer, &$length, &$offset) {
      do {
        $span= strcspn($buffer, $delimiters, $offset);
        if ($offset + $span + 1 < $length || 0 === $this->in->available()) {
          if (0 === $span) {
            return $buffer[$offset++] ?? null;
          } else {
            $token= substr($buffer, $offset, $span);
            $offset+= $span;
            return $token;
          }
        }
        $buffer.= $this->in->read(8192);
        $length= strlen($buffer);
      } while (true);
    };

    // Parse into tokens
    $line= 1;
    do {
      $token= $next(self::DELIMITERS);

      if ("\n" === $token) {
        $line++;
      } else if ("\r" === $token || "\t" === $token || ' ' === $token) {
        // Skip over whitespace
      } else if ("'" === $token || '"' === $token) {
        $string= $token;
        $end= '\\'.$token;
        do {
          $chunk= $next($end);
          if (null === $chunk) {
            throw new FormatException('Unclosed string literal starting at line '.$line);
          } else if ('\\' === $chunk) {
            $string.= $chunk.$next($end);
          } else {
            $string.= $chunk;
          }
        } while ($token !== $chunk);

        yield new Token($language->symbol('(literal)'), 'string', $string, $line);
        $line+= substr_count($string, "\n");
      } else if ('$' === $token) {
        yield new Token($language->symbol('(variable)'), 'variable', '$', $line);
      } else if ('#' === $token) {
        $t= $next(self::DELIMITERS);
        if ('[' === $t) {
          yield new Token($language->symbol('#['), 'operator', '#[', $line);
        } else {
          yield new Token(null, 'comment', '#'.$t.$next("\r\n"), $line);
        }
      } else if (0 === strcspn($token, '0123456789')) {
        if ('.' === ($t= $next(self::DELIMITERS))) {
          $number= 'decimal';
          $token.= '.'.$next(self::DELIMITERS);
        } else {
          null === $t || $offset-= strlen($t);
          $number= 'integer';
        }

        // Check for exponentation notation with + and -
        number: $e= strcspn($token, 'eE');
        if ($e === strlen($token) - 1) {
          $number= 'decimal';
          $t= $next(self::DELIMITERS);
          if ('-' === $t || '+' === $t) {
            $token.= $t.$next(self::DELIMITERS);
          } else {
            null === $t || $offset-= strlen($t);
          }
        } else if ($e < strlen($token)) {
          $number= 'decimal';
        }

        yield new Token($language->symbol('(literal)'), $number, str_replace('_', '', $token), $line);
      } else if (isset(self::OPERATORS[$token])) {

        // Resolve .5 (a floating point number) vs `.`, the concatenation operator
        // and C-style comments vs. `/` and `/=` division operators by looking ahead
        if ('.' === $token) {
          $t= $next(self::DELIMITERS);
          if (0 === strcspn($t, '0123456789')) {
            $token= ".$t";
            $number= 'decimal';
            goto number;
          }
          $offset-= strlen($t);
        } else if ('/' === $token) {
          $t= $next(self::DELIMITERS);
          if ('/' === $t) {
            yield new Token(null, 'comment', '//'.$next("\r\n"), $line);
            continue;
          } else if ('*' === $t) {
            $comment= '';
            do {
              $chunk= $next('/');
              $comment.= $chunk;
            } while (null !== $chunk && '*' !== $chunk[strlen($chunk) - 1]);
            $comment.= $next('/');
            yield new Token(null, '*' === $comment[0] ? 'apidoc' : 'comment', '/*'.$comment, $line);
            $line+= substr_count($comment, "\n");
            continue;
          }
          null === $t || $offset-= strlen($t);
        }

        // Handle combined operators. First, ensure we have enough bytes in our buffer
        // Our longest operator is 3 characters, hardcode this here.
        if (self::OPERATORS[$token]) {
          $offset--;
          while ($offset + 3 > $length && $this->in->available()) {
            $buffer.= $this->in->read(8192);
            $length= strlen($buffer);
          }
          foreach (self::OPERATORS[$token] as $operator) {
            if ($offset + strlen($operator) > $length) continue;
            if (0 === substr_compare($buffer, $operator, $offset, strlen($operator))) {
              $token= $operator;
              break;
            }
          }
          $offset+= strlen($token);
        }

        yield new Token($language->symbol($token), 'operator', $token, $line);
      } else {
        yield new Token($language->symbols[$token] ?? $language->symbol('(name)'), 'name', $token, $line);
      }
    } while ($offset < $length);
  }
}
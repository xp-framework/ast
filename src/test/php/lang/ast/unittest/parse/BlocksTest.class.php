<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{Block, InvokeExpression, Literal};
use test\{Assert, Test};

class BlocksTest extends ParseTest {

  #[Test]
  public function empty_block() {
    $this->assertParsed(
      [new Block([], self::LINE)],
      '{ }'
    );
  }

  #[Test]
  public function with_invoke() {
    $this->assertParsed(
      [new Block([new InvokeExpression(new Literal('block', self::LINE), [], self::LINE)], self::LINE)],
      '{ block(); }'
    );
  }
}
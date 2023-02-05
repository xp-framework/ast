<?php namespace lang\ast\unittest;

use lang\ast\{Language, Tokens, Errors};
use unittest\{Assert, Before, Expect, Test};

class LanguageTest {
  private $parse= [];

  #[Before]
  public function setupParsing() {
    $this->parse['test']= function($parse, $token) {
      $node= ['test' => $parse->token->value];
      $parse->forward();
      return $node;
    };
  }

  #[Test]
  public function symbols_initially_empty() {
    Assert::equals([], (new Language())->symbols);
  }

  #[Test]
  public function symbols_after_setting_up_parsing() {
    $fixture= new Language();
    $fixture->prefix('test', 0, $this->parse['test']);

    Assert::equals(['test' => $fixture->symbol('test')], $fixture->symbols);
  }

  #[Test]
  public function parse() {
    $fixture= new Language();
    $fixture->prefix('test', 0, $this->parse['test']);

    Assert::equals([['test' => 'one']], $fixture->parse(new Tokens('test one;'))->tree()->children());
  }

  #[Test, Expect(class: Errors::class, withMessage: '/Missing semicolon/')]
  public function missing_semicolon() {
    (new Language())->parse(new Tokens('test'))->tree();
  }
}
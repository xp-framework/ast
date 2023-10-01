<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\{Signature, Parameter};
use lang\ast\types\IsLiteral;
use test\{Assert, Test, Values};

class SignatureTest extends NodeTest {

  #[Test]
  public function can_create() {
    new Signature();
  }

  #[Test]
  public function without_parameters() {
    Assert::equals([], (new Signature())->parameters);
  }

  #[Test]
  public function parameters() {
    $params= [new Parameter('arg', null)];
    Assert::equals($params, (new Signature($params))->parameters);
  }

  #[Test]
  public function without_returns() {
    Assert::null((new Signature([]))->returns);
  }

  #[Test]
  public function returns_string() {
    $type= new IsLiteral('string');
    Assert::equals($type, (new Signature([], $type))->returns);
  }

  #[Test]
  public function without_by_ref() {
    Assert::false((new Signature([], null))->byref);
  }

  #[Test, Values([true, false])]
  public function by_ref($value) {
    Assert::equals($value, (new Signature([], null, $value))->byref);
  }

  #[Test]
  public function add_parameter() {
    $param= new Parameter('arg', null);

    $signature= new Signature();
    $signature->add($param);

    Assert::equals([$param], $signature->parameters);
  }

  #[Test]
  public function insert_parameter_at_beginning() {
    $a= new Parameter('a', null);
    $b= new Parameter('b', null);
    $c= new Parameter('c', null);

    $signature= new Signature([$a, $b]);
    $signature->insert(0, $c);

    Assert::equals([$c, $a, $b], $signature->parameters);
  }

  #[Test]
  public function insert_parameter_at_offset() {
    $a= new Parameter('a', null);
    $b= new Parameter('b', null);
    $c= new Parameter('c', null);

    $signature= new Signature([$a, $b]);
    $signature->insert(1, $c);

    Assert::equals([$a, $c, $b], $signature->parameters);
  }
}
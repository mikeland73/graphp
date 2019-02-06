<?php

use PHPUnit\Framework\TestCase;

abstract class GPTest extends TestCase {

  public function __construct() {
    parent::__construct();
    GPEnv::isTest();
  }

}

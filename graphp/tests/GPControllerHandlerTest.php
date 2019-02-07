<?php

class TestController extends GPController {

  public function foo($var) {

  }

}

class GPControllerHandlerTest extends GPTest {

  public function testURI() {
    $index = GPConfig::get()->use_index_php ? '/index.php' : '';
    $uri = TestController::URI()->foo('abc');
    $this->assertEquals($uri, $index.'/testcontroller/foo/abc');
  }

  public function testShortURI() {
    $index = GPConfig::get()->use_index_php ? '/index.php' : '';
    $uri = TestController::URI();
    $this->assertEquals($uri, $index.'/testcontroller/');
  }

  public function testBadURI() {
    $this->expectException(GPException::class);

    $uri = TestController::URI()->bar('abc');
  }

  public function testURL() {
    $index = GPConfig::get()->use_index_php ? '/index.php' : '';
    $domain = GPConfig::get()->domain;
    $uri = TestController::URL()->foo('abc');
    $this->assertEquals($uri, $domain.$index.'/testcontroller/foo/abc');
  }

  public function testShortURL() {
    $index = GPConfig::get()->use_index_php ? '/index.php' : '';
    $domain = GPConfig::get()->domain;
    $uri = TestController::URL();
    $this->assertEquals($uri, $domain.$index.'/testcontroller/');
  }

  public function testRedirect() {
    $handler = TestController::redirect();
    $this->assertTrue($handler instanceof GPRedirectControllerHandler);
    $handler->disableDestructRedirect();
  }

}

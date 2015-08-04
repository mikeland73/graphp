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

  /**
   * @expectedException GPException
   */
  public function testBadURI() {
    $uri = TestController::URI()->bar('abc');
  }

  public function testURL() {
    $index = GPConfig::get()->use_index_php ? '/index.php' : '';
    $domain = GPConfig::get()->domain;
    $uri = TestController::URL()->foo('abc');
    $this->assertEquals($uri, $domain.$index.'/testcontroller/foo/abc');
  }

  public function testRedirect() {
    $handler = TestController::redirect();
    $this->assertTrue($handler instanceof GPRedirectControllerHandler);
  }


}

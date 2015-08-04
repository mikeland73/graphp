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

}

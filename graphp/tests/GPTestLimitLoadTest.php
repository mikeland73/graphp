<?php

class GPTestLimitLoadModel extends GPNode {
  protected static function getEdgeTypesImpl() {
    return [
      new GPEdgeType(GPTestLimitLoadModel2::class),
    ];
  }
}

class GPTestLimitLoadModel2 extends GPNode {}

class GPTestLimitLoadTest extends GPTest {

  public static function setUpBeforeClass() {
    GPDatabase::get()->beginUnguardedWrites();
    GPNodeMap::addToMapForTest(GPTestLimitLoadModel::class);
    GPNodeMap::addToMapForTest(GPTestLimitLoadModel2::class);
  }

  public function testLimitLoad() {
    $m1 = new GPTestLimitLoadModel();
    $m21 = new GPTestLimitLoadModel2();
    $m22 = new GPTestLimitLoadModel2();
    $m23 = new GPTestLimitLoadModel2();
    batch($m1, $m21, $m22, $m23)->save();
    $m1->addGPTestLimitLoadModel2([$m21, $m22, $m23])->save();
    $m1->loadGPTestLimitLoadModel2(1);
    $this->assertEquals(count($m1->getGPTestLimitLoadModel2()), 1);
    $m1->forceLoadGPTestLimitLoadModel2();
    $this->assertEquals(count($m1->getGPTestLimitLoadModel2()), 3);
  }

  public function testLimitLoadIDs() {
    $m1 = new GPTestLimitLoadModel();
    $m21 = new GPTestLimitLoadModel2();
    $m22 = new GPTestLimitLoadModel2();
    $m23 = new GPTestLimitLoadModel2();
    batch($m1, $m21, $m22, $m23)->save();
    $m1->addGPTestLimitLoadModel2([$m21, $m22, $m23])->save();
    $m1->loadGPTestLimitLoadModel2IDs(1);
    $this->assertEquals(count($m1->getGPTestLimitLoadModel2IDs()), 1);
  }

  public static function tearDownAfterClass() {
    batch(GPTestLimitLoadModel::getAll())->delete();
    batch(GPTestLimitLoadModel2::getAll())->delete();
    GPDatabase::get()->endUnguardedWrites();
    GPDatabase::get()->dispose();
  }

}

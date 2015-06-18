<?php

class GPTestCountModel1 extends GPNode {
  protected static function getEdgeTypesImpl() {
    return [
      (new GPEdgeType(GPTestCountModel2::class))->setSingleNodeName('Foo'),
    ];
  }
}

class GPTestCountModel2 extends GPNode {

}

class GPEdgeCountTest extends GPTest {

  public static function setUpBeforeClass() {
    GPDatabase::get()->beginUnguardedWrites();
    GPNodeMap::addToMapForTest(GPTestCountModel1::class);
    GPNodeMap::addToMapForTest(GPTestCountModel2::class);
  }

  public function testCorrectCount() {
    $nodes = [];
    foreach (range(1, 10) as $key => $value) {
      $nodes[] = (new GPTestCountModel2())->save();
    }
    $n1 = (new GPTestCountModel1())->save();
    $n1->addGPTestCountModel2($nodes)->save();
    $count = $n1->getConnectedNodeCount(
      [GPTestCountModel1::getEdgeType(GPTestCountModel2::class)]);
    $this->assertEquals(idx0($count), 10);
  }

  public function testBatchCorrectCount() {
    $nodes = [];
    foreach (range(1, 10) as $key => $value) {
      $nodes[] = (new GPTestCountModel2())->save();
    }
    $n1 = (new GPTestCountModel1())->save();
    $n12 = (new GPTestCountModel1())->save();
    $n1->addGPTestCountModel2($nodes)->save();
    $n12->addGPTestCountModel2(array_slice($nodes, 5))->save();
    $count = batch($n1, $n12)->getConnectedNodeCount(
      [GPTestCountModel1::getEdgeType(GPTestCountModel2::class)]);
    $this->assertEquals(idx0($count[$n1->getID()]), 10);
    $this->assertEquals(idx0($count[$n12->getID()]), 5);
  }

  public static function tearDownAfterClass() {
    GPNode::simpleBatchDelete(GPTestCountModel1::getAll());
    GPNode::simpleBatchDelete(GPTestCountModel2::getAll());
    GPDatabase::get()->endUnguardedWrites();
  }

}
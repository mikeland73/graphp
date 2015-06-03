<?php

class GPEdgeInverseTestModel1 extends GPNode {
  protected static function getEdgeTypesImpl() {
    return [
      (new GPEdgeType(GPEdgeInverseTestModel2::class))
        ->inverse(GPEdgeInverseTestModel2::getEdgeType(
          GPEdgeInverseTestModel1::class
        )),
    ];
  }
}

class GPEdgeInverseTestModel2 extends GPNode {
  protected static function getEdgeTypesImpl() {
    return [
      (new GPEdgeType(GPEdgeInverseTestModel1::class)),
    ];
  }
}

class GPEdgeInverseTest extends GPTest {

  public static function setUpBeforeClass() {
    GPDatabase::get()->beginUnguardedWrites();
    GPNodeMap::addToMapForTest(GPEdgeInverseTestModel1::class);
    GPNodeMap::addToMapForTest(GPEdgeInverseTestModel2::class);
  }

  public function testAddingAndGetting() {
    $model1 = (new GPEdgeInverseTestModel1())->save();
    $model2 = (new GPEdgeInverseTestModel2())->save();
    $model1->addGPEdgeInverseTestModel2($model2)->save();
    $model2->loadGPEdgeInverseTestModel1();
    $this->assertEquals($model2->getOneGPEdgeInverseTestModel1(), $model1);
    $this->assertEquals(
      $model2->getGPEdgeInverseTestModel1(),
      [$model1->getID() => $model1]
    );
  }

  public function testEdgeRemoval() {
    $model1 = (new GPEdgeInverseTestModel1())->save();
    $model2 = (new GPEdgeInverseTestModel2())->save();
    $model3 = (new GPEdgeInverseTestModel2())->save();
    $model1->addGPEdgeInverseTestModel2([$model2, $model3])->save();
    $model1->loadGPEdgeInverseTestModel2();
    $model1->removeGPEdgeInverseTestModel2($model2)->save();
    // force loading should reset the edges
    $model2->forceLoadGPEdgeInverseTestModel1();
    $model3->forceLoadGPEdgeInverseTestModel1();
    $this->assertEquals($model2->getGPEdgeInverseTestModel1(), []);
    $this->assertEquals(
      $model3->getGPEdgeInverseTestModel1(),
      [$model1->getID() => $model1]
    );
  }

  public function testAllEdgeRemoval() {
    $model1 = (new GPEdgeInverseTestModel1())->save();
    $model2 = (new GPEdgeInverseTestModel2())->save();
    $model3 = (new GPEdgeInverseTestModel2())->save();
    $model1->addGPEdgeInverseTestModel2([$model2, $model3])->save();
    $model1->loadGPEdgeInverseTestModel2();
    $model1->removeAllGPEdgeInverseTestModel2()->save();
    // force loading should reset the edges
    $model1->forceLoadGPEdgeInverseTestModel2();
    $model2->forceLoadGPEdgeInverseTestModel1();
    $model3->forceLoadGPEdgeInverseTestModel1();
    $this->assertEmpty($model1->getGPEdgeInverseTestModel2());
    $this->assertEmpty($model2->getGPEdgeInverseTestModel1());
    $this->assertEmpty($model3->getGPEdgeInverseTestModel1());
  }

  public static function tearDownAfterClass() {
    GPNode::simpleBatchDelete(GPEdgeInverseTestModel1::getAll());
    GPNode::simpleBatchDelete(GPEdgeInverseTestModel2::getAll());
    GPDatabase::get()->endUnguardedWrites();
  }

}
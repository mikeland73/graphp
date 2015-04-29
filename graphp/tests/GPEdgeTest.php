<?

class GPTestModel1 extends GPNode {
  protected static function getEdgeTypesImpl() {
    return [
      (new GPEdgeType(GPTestModel2::class))->setSingleNodeName('Foo'),
    ];
  }
}

class GPTestModel2 extends GPNode {

}

class GPEdgeTest extends GPTest {

  public static function setUpBeforeClass() {
    GPDatabase::get()->beginUnguardedWrites();
    GPNodeMap::addToMapForTest(GPTestModel1::class);
    GPNodeMap::addToMapForTest(GPTestModel2::class);
  }

  public function testAddingAndGetting() {
    $model1 = (new GPTestModel1())->save();
    $model2 = (new GPTestModel2())->save();
    $model1->addGPTestModel2($model2)->save();
    $model1->loadGPTestModel2();
    $this->assertEquals($model1->getOneGPTestModel2(), $model2);
    $this->assertEquals(
      $model1->getGPTestModel2(),
      [$model2->getID() => $model2]
    );
  }

  public function testAddingAndGettingSingle() {
    $model1 = (new GPTestModel1())->save();
    $model2 = (new GPTestModel2())->save();
    $model1->addGPTestModel2($model2)->save();
    $model1->loadGPTestModel2();
    $this->assertEquals($model1->getFoo(), $model2);
    $this->assertEquals(
      $model1->getGPTestModel2(),
      [$model2->getID() => $model2]
    );
  }

  /**
   * @expectedException GPException
   */
  public function testAddingWrongType() {
    $model1 = (new GPTestModel1())->save();
    $model2 = (new GPTestModel1())->save();
    $model1->addGPTestModel2($model2)->save();
  }

  /**
   * @expectedException GPException
   */
  public function testAddngBeforeSaving() {
    $model1 = (new GPTestModel1())->save();
    $model2 = new GPTestModel2();
    $model1->addGPTestModel2($model2)->save();
  }

  public function testAddingAndGettingIDs() {
    $model1 = (new GPTestModel1())->save();
    $model2 = (new GPTestModel2())->save();
    $model1->addGPTestModel2($model2)->save();
    $model1->loadGPTestModel2IDs();
    $this->assertEquals($model1->getOneGPTestModel2IDs(), $model2->getID());
    $this->assertEquals(
      $model1->getGPTestModel2IDs(),
      [$model2->getID() => $model2->getID()]
    );
  }

  public function testEdgeRemoval() {
    $model1 = (new GPTestModel1())->save();
    $model2 = (new GPTestModel2())->save();
    $model3 = (new GPTestModel2())->save();
    $model1->addGPTestModel2([$model2, $model3])->save();
    $model1->loadGPTestModel2();
    $model1->removeGPTestModel2($model2)->save();
    // loading again should reset the edges
    $model1->loadGPTestModel2();
    $this->assertEquals(
      $model1->getGPTestModel2(),
      [$model3->getID() => $model3]
    );
  }

  public function testAllEdgeRemoval() {
    $model1 = (new GPTestModel1())->save();
    $model2 = (new GPTestModel2())->save();
    $model3 = (new GPTestModel2())->save();
    $model1->addGPTestModel2([$model2, $model3])->save();
    $model1->loadGPTestModel2();
    $model1->removeAllGPTestModel2()->save();
    // loading again should reset the edges
    $model1->loadGPTestModel2();
    $this->assertEmpty($model1->getGPTestModel2());
  }

  public static function tearDownAfterClass() {
    GPNode::simpleBatchDelete(GPTestModel1::getAll());
    GPNode::simpleBatchDelete(GPTestModel2::getAll());
    GPDatabase::get()->endUnguardedWrites();
  }

}
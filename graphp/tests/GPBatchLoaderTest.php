<?

class GPTestBatchLoaderModel extends GPNode {

  public static $customDelete = false;

  protected static function getDataTypesImpl() {
    return [
      new GPDataType('name', GPDataType::GP_STRING, true),
      new GPDataType('age', GPDataType::GP_INT),
    ];
  }

  protected static function getEdgeTypesImpl() {
    return [
      new GPEdgeType(GPTestBatchLoaderModel2::class),
    ];
  }

  public function delete() {
    parent::delete();
    self::$customDelete = true;
  }
}

class GPTestBatchLoaderModel2 extends GPNode {
  protected static function getEdgeTypesImpl() {
    return [
      new GPEdgeType(GPTestBatchLoaderModel3::class),
      new GPEdgeType(GPTestBatchLoaderModel4::class),
    ];
  }
}

class GPTestBatchLoaderModel3 extends GPNode {}
class GPTestBatchLoaderModel4 extends GPNode {}

class GPBatchLoaderTest extends GPTest {

  public static function setUpBeforeClass() {
    GPDatabase::get()->beginUnguardedWrites();
    GPNodeMap::addToMapForTest(GPTestBatchLoaderModel::class);
    GPNodeMap::addToMapForTest(GPTestBatchLoaderModel2::class);
    GPNodeMap::addToMapForTest(GPTestBatchLoaderModel3::class);
    GPNodeMap::addToMapForTest(GPTestBatchLoaderModel4::class);
  }

  public function testBatchSave() {
    $m1 = new GPTestBatchLoaderModel();
    $m2 = new GPTestBatchLoaderModel();
    GPNode::batchSave([$m1, $m2]);
    $this->assertNotEmpty($m1->getID());
    $this->assertNotEmpty($m2->getID());
  }

  public function testBatchDelete() {
    GPTestBatchLoaderModel::$customDelete = false;
    $m1 = new GPTestBatchLoaderModel();
    $m2 = new GPTestBatchLoaderModel();
    GPNode::batchSave([$m1, $m2]);
    $this->assertNotEmpty($m1->getID());
    $this->assertNotEmpty($m2->getID());
    GPNode::batchDelete([$m1, $m2]);
    $results = GPNode::multiGetByID([$m1->getID(), $m2->getID()]);
    $this->assertEmpty($results);
    $this->assertTrue(GPTestBatchLoaderModel::$customDelete);
  }

  public function testSimpleBatchDelete() {
    GPTestBatchLoaderModel::$customDelete = false;
    $m1 = new GPTestBatchLoaderModel();
    $m2 = new GPTestBatchLoaderModel();
    GPNode::batchSave([$m1, $m2]);
    $this->assertNotEmpty($m1->getID());
    $this->assertNotEmpty($m2->getID());
    GPNode::simpleBatchDelete([$m1, $m2]);
    $results = GPNode::multiGetByID([$m1->getID(), $m2->getID()]);
    $this->assertEmpty($results);
    $this->assertFalse(GPTestBatchLoaderModel::$customDelete);
  }

  public function testBatchLoad() {
    $m1 = new GPTestBatchLoaderModel();
    $m2 = new GPTestBatchLoaderModel2();
    $m3 = new GPTestBatchLoaderModel3();
    $m4 = new GPTestBatchLoaderModel4();
    GPNode::batchSave([$m1, $m2, $m3, $m4]);
    $m1->addGPTestBatchLoaderModel2($m2);
    $m2->addGPTestBatchLoaderModel3($m3);
    $m2->addGPTestBatchLoaderModel4($m4);
    GPNode::batchSave([$m1, $m2]);
    GPNode::batchLoadConnectedNodes(
      [$m1, $m2, $m3],
      array_merge(
        GPTestBatchLoaderModel::getEdgeTypes(),
        GPTestBatchLoaderModel2::getEdgeTypes()
      )
    );
    $this->assertNotEmpty($m1->getGPTestBatchLoaderModel2());
    $this->assertNotEmpty($m2->getGPTestBatchLoaderModel3());
    $this->assertNotEmpty($m2->getGPTestBatchLoaderModel4());
  }

  public static function tearDownAfterClass() {
    GPNode::simpleBatchDelete(GPTestBatchLoaderModel::getAll());
    GPNode::simpleBatchDelete(GPTestBatchLoaderModel2::getAll());
    GPNode::simpleBatchDelete(GPTestBatchLoaderModel3::getAll());
    GPNode::simpleBatchDelete(GPTestBatchLoaderModel4::getAll());
    GPDatabase::get()->endUnguardedWrites();
  }

}

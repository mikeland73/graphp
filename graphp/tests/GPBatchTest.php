<?

class GPTestBatchModel extends GPNode {

  public static $customDelete = false;

  protected static function getDataTypesImpl() {
    return [
      new GPDataType('name', GPDataType::GP_STRING, true),
      new GPDataType('age', GPDataType::GP_INT),
    ];
  }

  public function delete() {
    parent::delete();
    self::$customDelete = true;
  }
}

class GPBatchTest extends GPTest {

  public static function setUpBeforeClass() {
    GPDatabase::get()->beginUnguardedWrites();
    GPNodeMap::addToMapForTest(GPTestBatchModel::class);
  }

  public function testBatchSave() {
    $m1 = new GPTestBatchModel();
    $m2 = new GPTestBatchModel();
    GPNode::batchSave([$m1, $m2]);
    $this->assertNotEmpty($m1->getID());
    $this->assertNotEmpty($m2->getID());
  }

  public function testBatchDelete() {
    GPTestBatchModel::$customDelete = false;
    $m1 = new GPTestBatchModel();
    $m2 = new GPTestBatchModel();
    GPNode::batchSave([$m1, $m2]);
    $this->assertNotEmpty($m1->getID());
    $this->assertNotEmpty($m2->getID());
    GPNode::batchDelete([$m1, $m2]);
    $results = GPNode::multiGetByID([$m1->getID(), $m2->getID()]);
    $this->assertEmpty($results);
    $this->assertTrue(GPTestBatchModel::$customDelete);
  }

  public function testSimpleBatchDelete() {
    GPTestBatchModel::$customDelete = false;
    $m1 = new GPTestBatchModel();
    $m2 = new GPTestBatchModel();
    GPNode::batchSave([$m1, $m2]);
    $this->assertNotEmpty($m1->getID());
    $this->assertNotEmpty($m2->getID());
    GPNode::simpleBatchDelete([$m1, $m2]);
    $results = GPNode::multiGetByID([$m1->getID(), $m2->getID()]);
    $this->assertEmpty($results);
    $this->assertFalse(GPTestBatchModel::$customDelete);
  }

  public static function tearDownAfterClass() {
    GPNode::simpleBatchDelete(GPTestBatchModel::getAll());
    GPDatabase::get()->endUnguardedWrites();
  }

}

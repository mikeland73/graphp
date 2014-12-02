<?

class TestModel extends GPNode {
  protected static function getDataTypesImpl() {
    return [
      new GPDataType('name', GPDataType::GP_STRING, true),
      new GPDataType('age', GPDataType::GP_STRING),
    ];
  }
}

class ModelTest extends PHPUnit_Framework_TestCase {

  public static function setUpBeforeClass() {
    GPDatabase::get()->beginUnguardedWrites();
    GPNodeMap::addToMapForTest(TestModel::class);
  }

  public function testCreate() {
    $model = new TestModel();
    $this->assertEmpty($model->getID());
    $model->save();
    $this->assertNotEmpty($model->getID());
  }

  public function testLoadByID() {
    $model = new TestModel();
    $this->assertEmpty($model->getID());
    $model->save();
    $model::clearCache();
    $this->assertNotEmpty(TestModel::getByID($model->getID()));
    // And from cache
    $this->assertNotEmpty(TestModel::getByID($model->getID()));
  }

  public function testLoadByName() {
    $name = 'Weirds Name';
    $model = new TestModel(['name' => $name]);
    $this->assertEmpty($model->getID());
    $model->save();
    $model::clearCache();
    $this->assertNotEmpty(TestModel::getByName($name));
    // From cache
    $this->assertNotEmpty(TestModel::getByName($name));
  }

  /**
   * @expectedException GPException
   */
  public function testLoadByAge() {
    $model = new TestModel(['name' => 'name', 'age' => '18']);
    $this->assertEmpty($model->getID());
    $model->save();
    TestModel::getByAge('18');
  }

  public static function tearDownAfterClass() {
    foreach (TestModel::getAll() as $model) {
      $model->delete();
    }
    GPDatabase::get()->endUnguardedWrites();
  }

}

<?php

class GPTestModel extends GPNode {
  protected static function getDataTypesImpl() {
    return [
      new GPDataType('name', GPDataType::GP_STRING, true),
      new GPDataType('age', GPDataType::GP_INT),
      new GPDataType('currency', GPDataType::GP_STRING, false, 'USD'),
    ];
  }
}

class GPTestOtherModel extends GPNode {}

class GPNodeTest extends GPTest {

  public static function setUpBeforeClass() {
    GPDatabase::get()->beginUnguardedWrites();
    GPNodeMap::addToMapForTest(GPTestModel::class);
  }

  public function testCreate() {
    $model = new GPTestModel();
    $this->assertEmpty($model->getID());
    $model->save();
    $this->assertNotEmpty($model->getID());
  }

  public function testLoadByID() {
    $model = new GPTestModel();
    $this->assertEmpty($model->getID());
    $model->save();
    $model::clearCache();
    $this->assertNotEmpty(GPTestModel::getByID($model->getID()));
    // And from cache
    $this->assertNotEmpty(GPTestModel::getByID($model->getID()));
  }

  public function testLoadByIDWrongModel() {
    $model = new GPTestModel();
    $this->assertEmpty($model->getID());
    $model->save();
    $model::clearCache();
    $this->assertEmpty(GPTestOtherModel::getByID($model->getID()));
    // And from cache
    GPTestModel::getByID($model->getID());
    $this->assertEmpty(GPTestOtherModel::getByID($model->getID()));
  }

  public function testMultiLoadByID() {
    $model = new GPTestModel();
    $model2 = new GPTestModel();
    batch($model, $model2)->save();
    $model->save();
    $model::clearCache();
    $this->assertEquals(
      mpull(
        GPTestModel::multiGetByID([$model->getID(), $model2->getID()]),
        'getID'
      ),
      mpull([$model, $model2], 'getID', 'getID')
    );
    // And from cache
    $this->assertEquals(
      mpull(
        GPTestModel::multiGetByID([$model->getID(), $model2->getID()]),
        'getID'
      ),
      mpull([$model, $model2], 'getID', 'getID')
    );
  }

  public function testMultiLoadByIDWrongModel() {
    $model = new GPTestModel();
    $model2 = new GPTestModel();
    batch($model, $model2)->save();
    $model->save();
    $model::clearCache();
    $this->assertEmpty(
      GPTestOtherModel::multiGetByID([$model->getID(), $model2->getID()])
    );
    // And from cache
    GPTestModel::getByID($model->getID());
    $this->assertEmpty(
      GPTestOtherModel::multiGetByID([$model->getID(), $model2->getID()])
    );
    $this->assertNotEmpty(GPTestModel::multiGetByID([$model->getID()]));
  }

  public function testLoadByName() {
    $name = 'Weirds Name';
    $model = new GPTestModel(['name' => $name]);
    $this->assertEmpty($model->getID());
    $model->save();
    $model::clearCache();
    $this->assertNotEmpty(GPTestModel::getByName($name));
    // From cache
    $this->assertNotEmpty(GPTestModel::getByName($name));
  }

  public function testLoadByNameAfterUnset() {
    $name = 'Weirderer Name';
    $model = new GPTestModel(['name' => $name]);
    $this->assertEmpty($model->getID());
    $model->save();
    $model::clearCache();
    $loaded_model = GPTestModel::getOneByName($name);
    $this->assertNotEmpty($loaded_model);
    $loaded_model->unsetName();
    $loaded_model->save();
    $model::clearCache();
    $this->assertEmpty(GPTestModel::getOneByName($name));
  }

  /**
   * @expectedException GPException
   */
  public function testLoadByAge() {
    $model = new GPTestModel(['name' => 'name', 'age' => 18]);
    $this->assertEmpty($model->getID());
    $model->save();
    GPTestModel::getByAge(18);
  }

  public function testGetData() {
    $model = new GPTestModel(['name' => 'Foo', 'age' => 18]);
    $this->assertEquals($model->getName(), 'Foo');
    $this->assertEquals($model->getAge(), 18);
    $model->save();
    $model::clearCache();
    $loaded_model = GPTestModel::getByID($model->getID());
    $this->assertEquals(
      $model->getDataArray(),
      ['name' => 'Foo', 'age' => 18]
    );
  }

  public function testSetData() {
    $model = new GPTestModel();
    $model->setName('Bar');
    $model->setAge(25);
    $this->assertEquals(
      $model->getDataArray(),
      ['name' => 'Bar', 'age' => 25]
    );
  }

  public function testDefaultData() {
    $model = new GPTestModel();
    $this->assertEquals($model->getCurrency(), 'USD');
    $this->assertNull($model->getAge());
  }

  public static function tearDownAfterClass() {
    GPNode::simpleBatchDelete(GPTestModel::getAll());
    GPDatabase::get()->endUnguardedWrites();
  }

}

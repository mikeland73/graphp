<?php

class GPTestRangeModel extends GPNode {

  protected static function getDataTypesImpl() {
    return [
      GPDataType::string('firstName', true),
      GPDataType::int('age', true),
      GPDataType::string('sex', true),
    ];
  }
}

class GPLoadByRangeTest extends GPTest {

  public static function setUpBeforeClass(): void {
    GPDatabase::get()->beginUnguardedWrites();
    GPNodeMap::addToMapForTest(GPTestRangeModel::class);
  }

  public function testLoadByNameRange() {
    $m1 = (new GPTestRangeModel())->setfirstName('Andres')->save();
    $m2 = (new GPTestRangeModel())->setfirstName('Bibi')->save();
    $m3 = (new GPTestRangeModel())->setfirstName('Charlotte')->save();
    $results = GPTestRangeModel::getByFirstNameRange('Andres', 'zzz');
    $this->assertEquals(array_values($results), [$m1, $m2, $m3]);
    $results = GPTestRangeModel::getByFirstNameRange('Bibis', 'zzz');
    $this->assertEquals(array_values($results), [$m3]);
  }

  public function testLoadByAgeRangeWithLimit() {
    $m1 = (new GPTestRangeModel())->setAge(50)->save();
    $m2 = (new GPTestRangeModel())->setAge(26)->save();
    $m3 = (new GPTestRangeModel())->setAge(22)->save();
    $results = GPTestRangeModel::getByAgeRange(22, 50, 1);
    $this->assertEquals(array_values($results), [$m1]);
    $results = GPTestRangeModel::getByAgeRange(22, 50, 1, 1);
    $this->assertEquals(array_values($results), [$m2]);
    $results = GPTestRangeModel::getByAgeRange(22, 50, 3, 0);
    $this->assertEquals(array_values($results), [$m1, $m2, $m3]);
  }

  public function testgetAllWith() {
    $m1 = (new GPTestRangeModel())->setSex('M')->save();
    $m2 = (new GPTestRangeModel())->save();
    $m3 = (new GPTestRangeModel())->setSex('F')->save();
    $results = GPTestRangeModel::getAllWithSex();
    $this->assertEquals(array_values($results), [$m1, $m3]);
  }

  public static function tearDownAfterClass(): void {
    batch(GPTestRangeModel::getAll())->delete();
    GPDatabase::get()->endUnguardedWrites();
  }
}

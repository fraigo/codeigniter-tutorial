<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;

class TestModel extends CIUnitTestCase
{
    protected $modelClass = '';
    protected $model = null;
    protected $insertOk = [];
    protected $insertError = [];
    protected $updateOk = [];
    protected $updateError = [];
    static $insertId;

    protected function setUp(): void
    {
        parent::setUp(); // Do not forget
        $modelClass = $this->modelClass;
        if (!$modelClass){
            $this->markTestSkipped();
        } else {
            $this->model = new $modelClass();
        }
    }

    public function testList()
    {
        $list = $this->model->findAll();
        $this->assertGreaterThan(0,count($list));
    }

    public function testFindById()
    {
        $first = $this->model->first();
        $item = $this->model->where("id",$first['id'])->first();
        $this->assertEquals($first,$item);
    }

    public function testInsert()
    {
        $insertId = $this->model->insert($this->insertOk);
        static::$insertId = $insertId;
        $this->assertGreaterThan(0,$insertId,'Insert Error');
    }

    /**
     * @depends testInsert
     */
    public function testInsertError()
    {
        $insertId = $this->model->insert($this->insertError);
        $this->assertEquals(0,$insertId,'Error Expected');
    }

    /**
     * @depends testInsertError
     */
    public function testUpdate()
    {
        $item = $this->model->where("id",static::$insertId)->first();
        $result = $this->model->update(static::$insertId,$this->updateOk);
        $this->assertEquals(1,$result,'Update Error');
    }

    /**
     * @depends testUpdate
     */
    public function testUpdateError()
    {
        $result = $this->model->update(static::$insertId,$this->updateError);
        $this->assertEquals(0,$result,'Update Error');
    }

    /**
     * @depends testUpdate
     */
    public function testDelete()
    {
        $result = $this->model->delete(static::$insertId);
        $this->assertGreaterThan(0,$result,'Delete Error');
    }
}
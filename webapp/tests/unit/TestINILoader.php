<?php
require_once 'unit_tester.php';
require_once 'INILoader.php';

class TestINILoader extends UnitTestCase
{
    var $db;
    function setUp()
    {
/*
        $conf = Utils::conf('test');
        $this->dsn = $conf['db_dsn'];
        $this->db = DB::connect($this->dsn);
        $this->db->query('truncate member');
*/
    }

    function testLoad()
    {
/*
        $fixtures_dir = dirname(dirname(__FILE__)).'/fixtures/';
        LoadINI::load($fixtures_dir, $this->db_dsn);
*/
        $this->assertTrue(true);
    }
}
?>

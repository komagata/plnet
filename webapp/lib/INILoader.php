<?php
require_once 'DB.php';

class INILoader
{
    var $fixtures_dir;
    var $dsn;
    var $key_check;
    var $db;

    function INILoader($fixtures_dir, $dsn, $key_check = true)
    {
        $this->fixtures_dir = $fixtures_dir;
        $this->dsn = $dsn;
        $this->key_check = $key_check;
        $this->db =& DB::connect($dsn);
        $this->db->autoCommit(false);
        $this->db->setFetchMode(DB_FETCHMODE_ASSOC);
    }

    function load($name = null)
    {
        if (is_null($name)) {
            $this->db->query('BEGIN');
            $this->truncate_all();
            $this->load_all();
            $this->db->commit();
        } else {
            $this->db->query('BEGIN');
            $this->_truncate($name);
            $this->_load($name);
            $this->db->commit();
        }
    }

    function dump($name = null)
    {
        if (is_null($name)) {
            $this->_remove_all();
            $this->_dump_all();
        } else {
            $this->_remove($name);
            $this->_dump($name);
        }
    }

    function _load_all()
    {
        if (!$this->key_check and preg_match("/^mysql/", $this->dsn)) {
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        }

        foreach ($this->_get_ini_files() as $file) {
            list($name, $ext) = split("\.", $file);
            $this->_load($name);
        }

        if (!$this->key_check and preg_match("/^mysql/", $this->dsn)) {
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        }
    }

    function truncate_all()
    {
        foreach ($this->_get_ini_files() as $file) {
            list($name, $ext) = split("\.", $file);
            $this->_truncate($name);
        }
    }

    function _load($name)
    {
        echo "Load table {$name}... ";
        $data = parse_ini_file("{$this->fixtures_dir}{$name}.ini", true);
        foreach ($data as $fields) {
            if (DB::isError($res = $this->db->autoExecute($name, $fields))) {
                $this->db->rollback();
                echo "failed\n";
                trigger_error('INILoader::_load(): Failed to load. '
                .$res->toString(), E_USER_ERROR);
            }
        }
        echo "succeed\n";
    }

    function _truncate($name)
    {
        echo "Truncate table {$name}... ";
        $sql = "TRUNCATE $name";
        if (DB::isError($res = $this->db->query($sql))) {
            $this->db->rollback();
            echo "failed\n";
            trigger_error('INILoader::_truncate(): Failed to truncate. '
            .$res->toString(), E_USER_ERROR);
        }
        echo "succeed\n";
    }

    function _dump_all()
    {
        foreach ($this->db->getTables() as $table) {
            $this->_dump($table);
        }
    }

    function _remove_all()
    {
        foreach ($this->db->getTables() as $table) {
            $this->_remove($table);
        }
    }

    function _dump($name)
    {
        echo "Dump table {$name}... ";
        $file = "{$this->fixtures_dir}{$name}.ini";
        /*
        if (!is_writable($file)) {
            trigger_error("INILoader::dump(): Failed to open $file",
            E_USER_ERROR);
        }
        */
        $handle = fopen($file, 'a');
        $sql = "SELECT * FROM $name";
        foreach ($this->db->getAll($sql) as $index => $fields) {
            $line = "[{$name}".++$index."]\n";
            foreach ($fields as $key => $value) {
                $line .= "{$key} = \"{$value}\"\n";
            }

            if (!fwrite($handle, $line)) {
                trigger_error("INILoader::dump(): Failed to write $file",
                E_USER_ERROR);
            }
        }
        fclose($handle);
        echo "succeed\n";
    }

    function _remove($name)
    {
        echo "Remove file {$name}.ini ... ";
        unlink("{$this->fixtures_dir}{$name}.ini");
        echo "succeed\n";
    }

    function _get_ini_files()
    {
        $files = array();
        $d = dir($this->fixtures_dir);
        while ($entry = $d->read()) {
            if (preg_match("/\.ini$/", $entry)) {
                $files[] = $entry;
            }
        }
        $d->close();
        return $files;
    }
}

function load_ini($fixtures_dir, $dsn, $key_check = false)
{
    $loader =& new INILoader($fixtures_dir, $dsn, $key_check);
    $loader->load();
}

function dump_ini($fixtures_dir, $dsn)
{
    $loader =& new INILoader($fixtures_dir, $dsn);
    $loader->dump();
}
?>

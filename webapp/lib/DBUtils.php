<?php
require_once 'DB.php';

class DBUtils
{
    /**
     * initialize DataObject.ini
     *
     * @access private
     */
    function initialize()
    {
        global $dbo_options;

        include_once 'DB/DataObject.php';

        $options = &PEAR::getStaticProperty("DB_DataObject", "options");
        $options = $dbo_options;
    }

    function &connect($autoCommit = true)
    {
        global $conf;
        $db =& DB::connect($conf['dsn']);
        if (DB::isError($db)) {
            trigger_error('Failed to DB connect. ' . $db->toString(), E_USER_ERROR);
            return false;
        }
        $db->setFetchMode(DB_FETCHMODE_ASSOC);
        if ($autoCommit != true) $db->autoCommit(false);
        return $db;
    }

    function get($table, $opt)
    {
        $t = DB_DataObject::factory($table);
        if (is_array($opt)) {
            $wheres = array();
            foreach ($opt as $key => $value)
                $wheres[] = "$key = ".$t->escape($value);

            $results =  DBUtils::find($table, implode(' AND ', $wheres));
            if (count($results) == 1) {
                return $results[0];
            } else {
                return $results;
            }
        } else {
            $t->get($opt);
            return $t;
        }
    }

    function find($table, $where = null)
    {
        $t = DB_DataObject::factory($table);
        if ($where) $t->whereAdd($where);
        $t->find();
        $results = array();
        while ($t->fetch()) $results[] = $t;
        return count($results) > 0 ? $results : null;
    }
}
?>

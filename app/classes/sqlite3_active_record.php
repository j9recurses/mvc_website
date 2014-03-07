<?php

//
// All of the code in this class is SQLite 3 specific.
//

if(!class_exists('BASE')) {
    /**
     * The Base class provides some low level methods such as __get and __set
     * so that we can don't have to go through this hassle (did I show some
     * annoyance?) of creating these methods for other classes in our site.
     *
     * $Id: base.php,v 1.1.1.1 2008/03/11 20:52:42 somedude Exp $
     */
    class Base {
        private $props = array();
        function __get($name) {
            if (isset($this->props[$name])) {
                return $this->props[$name];
            } else {
                return null;
            }
        }
        function __set($name, $value) {
            $this->props[$name] = $value;
        }
    }
}

// SQLITE Active Record class
class SQLite3_Active_Record extends Base {
    public $fields = array();
    public $fields_types = array();

    function __construct() {
        try {
            $this->dbc = new SQLite3(DB_FILE);
        } catch(Exception $e) {
            die($e->getmessage());
        }

        // Determine table
        $this->table =  strtolower(str_replace('__','',get_class($this))) . 's';

        if(!$this->table_exists($this->dbc,$this->table)) {
            die('Database error: no table named ' . $this->table);
        }
        else {
            $sql = "PRAGMA table_info('{$this->table}')";
            $res = $this->dbc->query($sql);

            while($row = $res->fetchArray(SQLITE_ASSOC)) {
                $this->fields[] = $row['name'];
                $this->fields_types[$row['name']] = $row['type'];
            }
        }
    }

    function table_exists() {
        $sql = "SELECT * FROM sqlite_master
            WHERE type='table'
            AND name='". sqlite_escape_string($this->table) ."'";

        $res = $this->dbc->querySingle($sql);
        if($res) {
            return true;
        }
        return false;
    }

    /**
     * Handle virtual functions
     * @param <method> $method
     * @param <array> $args
     * @return <Sqlite_Active_Record>
     */
    function __call($method, $args) {
        if(stristr($method,'_by_page')) {
            $field = substr($str, strlen('find_'),strlen($str) - stripos($str,'_by_page'));
            if(!in_array($this->fields,$field)) return false;
            $sql = "SELECT * FROM " . $this->table ." WHERE $field = '{$args[0]}'";
            $res = $this->dbc->query($sql,SQLITE_ASSOC);

            $out = array();
            while($row = $res->fetch()) {
                $tmp = new $this;
                foreach($this->fields as $f) {
                    $tmp->$f = $row[$f];
                }
                $out[] = $tmp;
            }
        }
        elseif(stristr($method,'find_all') !== false) {
            $sql = "SELECT * FROM " . $this->table  . ' ORDER BY created_at DESC LIMIT 10' ;
            $res=$this->dbc->query($sql);
            $out = array();
            while($row = $res->fetchArray(SQLITE_ASSOC))  {
                $tmp = new $this;
                foreach($this->fields as $f) {
                    $tmp->$f = $row[$f];
                }
                $out[] = $tmp;
            }
            return $out;
        }
        elseif(stristr($method,'find_by_')) {
            $field = substr($method,strlen('find_by_'));

            if($field == 'sql') {
                $res = $this->dbc->query($args[0]);
            }
            else {
                $sql = "SELECT * FROM " . $this->table ." WHERE $field = '{$args[0]}'";
                $res = $this->dbc->query($sql);
            }

            $out = array();
            while($row = $res->fetchArray(SQLITE_ASSOC)) {
                $tmp = new $this;
                foreach($this->fields as $f) {
                    $tmp->$f = $row[$f];
                }
                $out[] = $tmp;
            }
            if(!empty($out) && $field == 'id') return $out[0];
            return $out;
        }
    }


    function update()
    {
        // This is an update
    //    print "UPDATING";
        $setpairs = array();
        foreach($this->fields as $f) {
            $setpairs[] = " $f='". $this->$f ."'";
        }
        $sql = 'UPDATE ' . $this->table . ' set ' .implode(',',$setpairs) . ' WHERE id = '. $this->id;

        $res = $this->dbc->exec($sql);
        if($res)   return true;
        return false;
    }

    function insert() 
    {
        $sql = "INSERT INTO " . $this->table . "(";
        $keys = array();
        $values = array();

        foreach($this->fields as $f) {
            if($f == 'id') continue;
            $keys[] = $f;
            $values[] = SQLITE_ESCAPE_STRING(htmlentities($this->$f));
        }

        $sql .= "id," . implode(",",$keys) . ") values(NULL, ";
        $kstring = array();
        $vstring = array();

        for($i=0;$i < count($keys); $i++ ) {
            $k = $keys[$i];
            $kstring[] = "'$k'";

            if($k == 'id') {
                continue;
                $val = 'Null';
            }
            elseif($k == 'created_at' || $k == 'modified_at') {
                $val = "datetime('now')";
            }
            else {
                $val = "'" . SQLITE_ESCAPE_STRING(htmlentities($this->$k)) . "'";
            }
            $vstring[] = $val;
        }
        $sql .= implode(',',$vstring).")";
        $res = $this->dbc->query($sql);
        if($res === false) return false;
        $this->id =  $this->dbc->lastinsertRowId();
        // if we wanted insert related data into another table
        // we would use the object id to relate that data.
        
        return true;
    }

    function delete($id = null) {
        if(!is_null($id)) {
            $sql = 'DELETE FROM ' . $this->table . ' WHERE id = ' . $id;
        }
        else {
            $sql = 'DELETE FROM ' . $this->table . ' WHERE id = ' . $this->id;
        }
        $this->dbc->exec($sql);
    }

    function save() {
        // Manufacture CREATE or UPDATE sql
        
        // UPDATE
        if($this->id) {
         if($this->update()) return true;
        }
        else {
          if($this->insert()) return true;
        }
        return false;
    }

    function fields() {
        return $this->fields;
    }
}

if(isset($argv) && $argv[0] == basename(__FILE__)) {
    // Try to keep this class and its dependencies self-contained.
    if(!defined('DB_FILE')) {
        define('DB_FILE', dirname(__FILE__).'/sqlite.db');
        function table_exists($dbc,$table) {
            $sql = "SELECT * FROM sqlite_master
            WHERE type='table'
            AND name='". sqlite_escape_string($table) ."'";

            $res = $dbc->querySingle($sql);
            if($res) {
                return true;
            }
            return false;
        }

        define( 'CREATE_USERS_SQL',
                "create table users (
            id integer not null primary key,
            user_name text not null,
            first_name text not null,
            last_name text not null,
            email text not null,
            password text not null,
            created_at datetime not null)"
        );

        $dbc = new SQlite3(DB_FILE);
        if(!table_exists($dbc,'users')) {
            $dbc->query(CREATE_USERS_SQL);
            // some dummy data for testing
            /*
            $dbc->query("INSERT INTO users values(NULL,'admin','Administrator','Administrator','admin@ccsf.edu','".sha1('ExtraSecret')."',datetime('now'))");
            $dbc->query("INSERT INTO users values(NULL,'test','Test','Test','test@ccsf.edu','".sha1('secret')."',datetime('now'))");
            $dbc->query("INSERT INTO users values(NULL,'doug','Douglas','Putnam','test@ccsf.edu','".sha1('secret')."',datetime('now'))");
             */
        }
    }

    $dbc = new Sqlite3(DB_FILE);
    if(!table_exists($dbc,'users')) {
        print 'Create a users table for testing.';
    }
    else {
        // A test Model for the users table
        class User extends SQLITE3_ACTIVE_RECORD {
            function password($password) {
                $this->password = sha1($password);
            }
        }

        $u = new User;
        // Find new user
        $u->user_name = 'admin';
        $u->first_name = 'joe';
        $u->last_name = 'Administrator';
        $u->email = 'joe@admin.com';
        $u->password = 'secret';
        $u->save();
        $u = $u->find_by_id(1);
        var_dump($u);
        // get class properties 
        $fields = $u->fields();

        foreach($fields as $f) {
            print "$f\t{$u->$f}\n";
        }

        $u->password = 'this is top secret';
        $u->save();
        $all = $u->find_all();
        
        foreach($all as $a) {
            foreach($a as $k => $v) {
                foreach($fields as $f) {
                    print $f . ' = ' . $a->$f . "\n";
                }
            }
        }
        $u->password('another password');
        $u->save();
        $u->password('yet another');
        $u->save();
        var_dump($u);
        print "\n";
        $sha = sha1('another password');
        print "Setting a new password\n";
        $passd = $u->password;
        print $u->id;
        print "\n";
        print "\n";
        print $u->delete();
        $u = new User;
        $u = $u->find_by_id(1);
        var_dump($u);
    }
    unlink(DB_FILE);
}

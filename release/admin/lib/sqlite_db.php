<?php
class SqliteDB{
	var $db;
	function SqliteDB($dbName){
		global $db;
		if(!isset($db)){
			$this->db=$db=new SQLiteDatabase($dbName);
			
		}else{
			$this->db=$db;
		}
	}
    function alter_table($args){
        /*
         * SQLite does not support ALTER TABLE; this is a helper query
         * to handle this. 'table' represents the table name, 'rows'
         * the news rows to create, 'save' the row(s) to keep _with_
         * the data.
         *
         * Use like:
         * $args = array(
         *     'table' => $table,
         *     'rows'  => "id INTEGER PRIMARY KEY, firstname TEXT, surname TEXT, datetime TEXT",
         *     'save'  => "NULL, titel, content, datetime"
         * );
         * $res = $db->query( $db->getSpecialQuery('alter', $args));
         */

        $q = array(
            'BEGIN TRANSACTION',
            "CREATE TEMPORARY TABLE {$args['table']}_backup ({$args['rows']})",
            "INSERT INTO {$args['table']}_backup SELECT {$args['save']} FROM {$args['table']}",
            "DROP TABLE {$args['table']}",
            "CREATE TABLE {$args['table']} ({$args['rows']})",
            "INSERT INTO {$args['table']} SELECT {$args['rows']} FROM {$args['table']}_backup",
            "DROP TABLE {$args['table']}_backup",
            'COMMIT',
        );

        // This is a dirty hack, since the above query will no get executed with a single
        // query call; so here the query method will be called directly and return a select instead.
        foreach ($q as $query) {
            echo $query;
            $this->query($query);
        }
        return "SELECT * FROM {$args['table']};";
    }

    function query($query){
		return $this->db->query($query);
	}
}

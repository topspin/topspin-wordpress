<?php
/*
 *	Last Modified:		January 9, 2011
 *
 *	----------------------------------
 *	Change Log
 *	----------------------------------
 *	2012-01-09
 		- Updated topspin_table_column_add
 *	2011-09-07
 		- Fixed topspin_table_column_add column (removed AFTER key)
 *	2011-07-26
 		- File created
 		- New function topspin_table_column_exists()
 		- New function topspin_table_column_add()
 */

function topspin_table_column_exists($table,$column) {
	/*
	 *	Checks to see if a table column exists
	 *
	 *	PARAMETERS:
	 *		@table (string)
	 *		@column (string)
	 *
	 *	RETURNS:
	 *		true if the column exists in the table
	 *		false otherwise
	 */
	global $wpdb;
	$sql = <<<EOD
SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE
	TABLE_SCHEMA = '{$wpdb->dbname}'
	AND TABLE_NAME = '{$wpdb->prefix}{$table}'
	AND COLUMN_NAME = '{$column}'
EOD;
	$res = $wpdb->get_var($sql);
	return ($res) ? true : false;
}

function topspin_table_column_add($table,$column,$type='text',$options=null) {
	/*
	 *	Checks to see if a table column exists
	 *
	 *	PARAMETERS:
	 *		@table (string)
	 *		@column (string)
	 *		@type (string)				enumeration: INT, BIGINT, VARCHAR(255), TEXT, LONGTEXT, TIMESTAMP (default: TEXT)
	 *		@options (array)			Additional key options
	 *			@first (bool)			Add to the beginning of table?
	 *			@autoIncrement (bool)	Add auto increment?
	 *			@primaryKey (bool)		Make it as a primary key?
	 *
	 *	RETURNS:
	 *		true if the column exists in the table
	 *		false otherwise
	 */
	global $wpdb;
	$defaults = array(
		'first' => false,
		'autoIncrement' => false,
		'primaryKey' => false
	);
	$options = array_merge($defaults,$options);
	$first = ($options['first']) ? 'FIRST' : '';
	$autoIncrement = ($options['autoIncrement']) ? 'AUTO_INCREMENT' : '';
	$primaryKey = ($options['primaryKey']) ? 'ADD PRIMARY KEY (`'.$column.'`)' : '';
$sql = <<<EOD
ALTER TABLE `{$wpdb->prefix}{$table}` ADD `{$column}` {$type} NOT NULL {$first} {$autoIncrement} {$primaryKey};
EOD;
	$wpdb->query($sql);
}

?>
<?php
/*
 *	Last Modified:		July 26, 2011
 *
 *	----------------------------------
 *	Change Log
 *	----------------------------------
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

function topspin_table_column_add($table,$column,$type='text') {
	/*
	 *	Checks to see if a table column exists
	 *
	 *	PARAMETERS:
	 *		@table (string)
	 *		@column (string)
	 *		@type (string)			enumeration: INT, BIGINT, VARCHAR(255), TEXT, LONGTEXT (default: TEXT)
	 *
	 *	RETURNS:
	 *		true if the column exists in the table
	 *		false otherwise
	 */
	 global $wpdb;
$sql = <<<EOD
ALTER TABLE  `{$wpdb->prefix}{$table}` ADD  `{$column}` {$type} NOT NULL AFTER  `id` ;
EOD;
$wpdb->query($sql);
}

?>
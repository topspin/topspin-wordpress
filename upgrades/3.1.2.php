<?php

/*
 *	3.1.2 UPGRADE NOTICE
 *	--------------------
 *
 	2012-01-26
	 	- Remove rerun scripts (2012-01-26)
	2011
	 	- Forces re-run of all past upgrade scripts
 */

update_option('topspin_version',TOPSPIN_VERSION);
update_option('topspin_update_check',1);

?>
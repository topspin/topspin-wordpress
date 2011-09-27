<?php

/*
 *	3.3.2 UPGRADE NOTICE
 *	--------------------
 *	
 	- Runs the Fix Upgrade script (and skip current)
 */

global $store;

topspin_rerun_upgrades(1);

?>
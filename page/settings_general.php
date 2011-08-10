<?php

/*
 *	Last Modified:		August 1, 2011
 *
 *	----------------------------------
 *	Change Log
 *	----------------------------------
 *	2011-08-01
 		- Updated saving to delete all currently created store if the artist ID is switched.
 *	2011-04-11
 		- Added new button for force rerun upgrade scripts
 *	2011-04-06
 		- Updated Artist ID field to a dropdown of available artists
 		- Moved API credential fields to the top
 		- Updated submit to auto-set artist ID on first save
 		- Added Template description
 *	2011-04-05
 		- Added a new field called "Template"
 */

global $store;
$success = '';

if($_SERVER['REQUEST_METHOD']=='POST') {
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case "rerun_upgrades":
				if(isset($_POST['fix_upgrades'])) { topspin_rerun_upgrades(); }
				$success = 'Plugin upgrade scripts successfully ran.';
				break;
			case "rebuild_cache":
				if(isset($_POST['cache_all'])) { $store->rebuildAll(); }
				$success = 'Cache successfully updated.';
				break;
			case "general_settings":
			default:
				unset($_POST['action']);

				## Empty all stores and store settings if different artist ID is set
				if($_POST['topspin_artist_id']!=$store->getSetting('topspin_artist_id')) {
					$stores = $store->getStores('all');
					foreach($stores as $_store) {
						$store->deleteStore($_store->store_id,1);
						wp_delete_post($_store->ID,1);	//deletes the page from the posts table
					}
				}

				## Set Each Option
				foreach($_POST as $key=>$value) {
					$store->setSetting($key,$value); //Update all posted settings on this page.
					update_option($key,$value); //Update WordPress options table (v2.0)
				}
				$store->rebuildAll();

				##	If Artists is Unset (New)
				if(!isset($_POST['topspin_artist_id'])) {
					$artistsList = $store->getArtistsList();
					$store->setSetting('topspin_artist_id',$artistsList[0]['id']);
					update_option('topspin_artist_id',$artistsList[0]['id']);
				}
				$success = 'Settings saved.';
				break;
		}
	}
}
$apiError = '';
$apiStatus = $store->checkAPI();
if($apiStatus) { $apiError = $apiStatus->error_detail; }
?>

<div class="wrap">
    <h2>Topspin General Settings</h2>

    <?php if($success) : ?><div class="updated settings-error"><p><strong><?php echo $success; ?></strong></p></div><?php endif; ?>

    <?php if($apiError) : ?><div class="error settings-error"><p><strong><?php echo $apiError; ?></strong></p></div><?php endif; ?>

    <form name="topspin_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
    <input type="hidden" name="action" value="general_settings" />
	<?php
	$artistsList = $store->getArtistsList();
	$totalArtists = count($artistsList);
	?>
    <h3>Topspin API Settings</h3>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row"><label for="topspin_api_username">Topspin API User</label></th>
                <td>
                    <input id="topspin_api_username" class="regular-text" type="text" value="<?php echo $store->getSetting('topspin_api_username'); ?>" name="topspin_api_username" />
                    <span class="description">Go to <a href="http://app.topspin.net/account/profile/" target="_blank">Account Settings</a> to obtain your API credentials.</span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="topspin_api_key">Topspin API Key</label></th>
                <td>
                    <input id="topspin_api_key" class="regular-text" type="text" value="<?php echo $store->getSetting('topspin_api_key'); ?>" name="topspin_api_key" />
                    <span class="description">Go to <a href="http://app.topspin.net/account/profile/" target="_blank">Account Settings</a> to obtain your API credentials.</span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="topspin_artist_id">Topspin Artist</label></th>
                <td>
					<?php if(count($artistsList)>1) : ?>
					<select id="topspin_artist_id" name="topspin_artist_id">
	                	<?php $selected_artist = $store->getSetting('topspin_artist_id');
	                	foreach($artistsList as $artist) : $artist_selected=($selected_artist==$artist['id'])?'selected="selected"':''; ?>
	                		<option value="<?php echo $artist['id'];?>" <?php echo $artist_selected;?>><?php echo $artist['name'];?> (<?php echo $artist['id'];?>)</option>
	                	<?php endforeach; ?>
	                </select>
                    <div class="description">
	                    PLEASE NOTE: You have multiple Artist IDs associated with your API user / key combination.  You can easily select which artist to use for your store, however if you change the Artist after you have already created Store Pages, those pages will become blank since the old Artist's Offers will no longer exist in the plugin's cache.  You'll need to edit those pages and rebuild the Offers. 
                    </div>
                    <?php elseif($totalArtists && $totalArtists==1) : ?>
                   	<input type="hidden" name="topspin_artist_id" value="<?php echo $artistsList[0]['id'];?>" />
                    <input class="artist-name regular-text" type="text" disabled="disabled" value="<?php echo $artistsList[0]['name'];?> (<?php echo $artistsList[0]['id'];?>)" />
					<?php endif; ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="topspin_api_key">Template:</label></th>
                <td>
                	<?php
                	$selectedTemplate = $store->getSetting('topspin_template_mode');
                	?>
                    <select id="topspin_template_mode" name="topspin_template_mode">
                    	<option value="simplified" <?php echo ($selectedTemplate=='simplified')?'selected="selected"':'';?>>Simplified</option>
                    	<option value="standard" <?php echo ($selectedTemplate=='standard')?'selected="selected"':'';?>>Standard</option>
                    </select>
                    <div class="description">
                    	<?php
                    	$simplified_display = ($selectedTemplate=='simplified') ? '' : 'hide';
                    	$standard_display = ($selectedTemplate=='standard') ? '' : 'hide';
                    	?>
                    	<div class="template-simplified <?php echo $simplified_display;?>">
							<p>
								<strong><em>This Simplified Template is designed to give the best out-of-the box store layout if you prefer to do little or no customization.</em></strong>
								It uses HTML Tables to construct the Store Grid and therefore is less flexible for developers who wish to heavily customize the store's output. 
							</p>
							<p>
								This template can be customized just like the Standard Template, following these steps: <br/>
								1) Copy the /topspin-simplified/ directory from the Plugin's /templates/ folder to your site's active theme folder<br/>
								2) Edit the .php and .css files in this new /topspin-simplified/ directory in your site's active theme folder - this will override the defaults
							</p>
							<p><strong><em>PLEASE NOTE: Do NOT edit the files directly in the Plugin's /templates/ folder!  When you upgrade, all of your customizations will be lost if you do!</em></strong></p>
						</div>
                    	<div class="template-standard <?php echo $standard_display;?>">
                    		<p>
                    			<strong><em>This Standard Template is designed as a skeleton framework with the Developer in mind, allowing for the most flexibility for template customizations.</em></strong>
                    			It uses floating HTML Divs to construct the Store Grid rather than Tables, making it easier to manipulate.
                    		</p>
                    		<p><strong><em>If you are looking to run the plugin out-of-the-box with little or no customization, please user the Simplified Template for the best front-end output and alignment of images and buy buttons.</em></strong></p>
							<p>
								This template can be fully customized following these steps: <br/>
								1) Copy the /topspin-standard/ directory from the Plugin's /templates/ folder to your site's active theme folder<br/>
								2) Edit the .php and .css files in this new /topspin-standard/ directory in your site's active theme folder - this will override the defaults
							</p>
							<p><strong><em>PLEASE NOTE: Do NOT edit the files directly in the Plugin's /templates/ folder!  When you upgrade, all of your customizations will be lost if you do!</em></strong></p>
							<p>(<strong><em>Backward Compatibility:</em></strong> For users upgrading from a version pre-v3.1, the Plugin will still recognize your customized topspin.css, topspin-ie7.css, featured-item.php and item-listings.php files located in your site's active theme folder even if they aren't in the /topspin-standard/ sub-folder.)</p>
                    	</div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" /></p>
    </form>

    <h3>Database Cache</h3>
    <table class="form-table">
		<form name="topspin_form_rebuild_cache" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
	    <input type="hidden" name="action" value="rebuild_cache" />
    	<tbody>
        	<tr valign="top">
            	<th scope="row"><label>Rebuild Database</label></th>
                <td>
					<input type="submit" name="cache_all" class="button-primary" value="<?php _e('Rebuild'); ?>" />
                    <span class="description"><?php echo ($last_cache_all=$store->getSetting('topspin_last_cache_all')) ? 'Last built: '.date('Y-m-d h:i:sa',$last_cache_all+(3600*get_option('gmt_offset'))) : 'No action yet.'; ?></span>
                </td>
            </tr>
        </tbody>
		</form>
		<form name="topspin_form_rerun_upgrades" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
	    <input type="hidden" name="action" value="rerun_upgrades" />
    	<tbody>
        	<tr valign="top">
            	<th scope="row"><label>Fix Plugin Installation</label></th>
                <td>
					<input type="submit" name="fix_upgrades" class="button-primary" value="<?php _e('Fix'); ?>" />
                    <span class="description">Use this if you are experiencing problems with the plugin.  In an attempt to fix your installation, this will force WordPress to re-run all of the upgrade scripts and rebuild the cache.</span>
                </td>
            </tr>
        </tbody>
		</form>
    </table>
    
</div>

<script type="text/javascript" language="javascript">
jQuery(function($) {
	$('#topspin_template_mode').change(function() {
		var open = $(this).val();
		var close = (open=='simplified') ? 'standard' : 'simplified';
		$('.template-'+close).slideUp(function() {
			$('.template-'+open).slideDown();
		});
	});
});
</script>

<?php
global $store;
$success = '';
if($_SERVER['REQUEST_METHOD']=='POST') {
	if(isset($_POST['action'])) {
		switch($_POST['action']) {
			case "rebuild_cache":
				if(isset($_POST['cache_all'])) { $store->rebuildAll(); }
				$success = 'Cache successfully updated.';
				break;
			case "general_settings":
			default:
				unset($_POST['action']);
				foreach($_POST as $key=>$value) {
					$store->setSetting($key,$value); //Update all posted settings on this page.
					update_option($key,$value); //Update WordPress options table (v2.0)
				}
				$store->rebuildAll();
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

    <form name="topspin_form" method="post" action="<?=$_SERVER['REQUEST_URI'];?>">
    <input type="hidden" name="action" value="general_settings" />
    <h3>Topspin API Settings</h3>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row"><label for="topspin_artist_id">Topspin Artist ID</label></th>
                <td>
                    <input id="topspin_artist_id" class="regular-text" type="text" value="<?php echo $store->getSetting('topspin_artist_id'); ?>" name="topspin_artist_id" />
                    <span class="description">Go to <a href="http://app.topspin.net/account/settings/" target="_blank">Account Profile</a> to obtain your Artist ID.</span>
                </td>
            </tr>
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
        </tbody>
    </table>

    <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" /></p>
    </form>

	<form name="topspin_form_rebuild_cache" method="post" action="<?=$_SERVER['REQUEST_URI'];?>">
    <input type="hidden" name="action" value="rebuild_cache" />
    <h3>Database Cache</h3>
    <table class="form-table">
    	<tbody>
        	<tr valign="top">
            	<th scope="row"><label>Rebuild Database</label></th>
                <td>
					<input type="submit" name="cache_all" class="button-primary" value="<?php _e('Rebuild'); ?>" />
                    <span class="description"><?php echo ($last_cache_all=$store->getSetting('topspin_last_cache_all')) ? 'Last built: '.date('Y-m-d h:i:sa',$last_cache_all+(3600*get_option('gmt_offset'))) : 'No action yet.'; ?></span>
                </td>
            </tr>
        </tbody>
    </table>
	</form>
    
</div>
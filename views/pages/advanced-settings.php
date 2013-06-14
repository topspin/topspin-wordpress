<div class="wrap">

	<form method="post" action="options.php">
	<?php settings_fields('topspin_advanced_settings'); ?>

	<div id="topspin-advanced-settings">
		<h2>Advanced Settings</h2>

		<div class="description">
			<p>If you do not know what these settings are, please do not touch.</p>
		</div>

		<table class="form-table">
			<tr id="topspin-advanced-settings-api-prefetching" valign="top">
				<th scope="row">Enable Prefetching:</th>
				<td>
					<label>
						<input type="checkbox" name="topspin_api_prefetching" <?php if(TOPSPIN_API_PREFETCHING) : ?>checked="checked"<?php endif; ?> />
						Prefetching increases caching speed and performance by hitting the API before the actual caching process to store a local file for the caching script to read from.
					</label>
				</td>
			</tr>
		</table>
	</div>

	<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
	</form>
	
	<div id="topspin-advanced-settings-api-prefetching-group">
		<h2>API Prefetching</h2>
		<table class="form-table">
			<tr id="topspin-advanced-settings-purge-prefetch" valign="top">
				<th scope="row">Purge Prefetch:</th>
				<td>
					<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
				    <input type="hidden" name="topspin_post_action" value="purge_prefetch" />
					<button type="submit" class="button-primary"><?php _e('Purge All'); ?></button>
                    <span class="description">Purge all prefetched files</span>
					</form>
				</td>
			</tr>
		</table>
	</div>

</div>
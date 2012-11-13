<div class="wrap">

    <h2>Cron Schedules</h2>
    <table class="form-table">
    	<tbody>
        	<tr valign="top">
            	<th scope="row"><label>Next Hit:</label></th>
                <td>
					<?php echo WP_Topspin_Cron::nextHit(); ?>
                </td>
            </tr>
        	<tr valign="top">
            	<th scope="row"><label>Reset Cron</label></th>
                <td>
					<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
				    <input type="hidden" name="topspin_post_action" value="reset_cron" />
					<button type="submit" class="button-primary"><?php _e('Reset'); ?></button>
					</form>
                </td>
            </tr>
        </tbody>
    </table>

</div>
<div class="wrap">

  <h2>Cron Schedules</h2>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th width="100" scope="row"><label>Next Offers Hit:</label></th>
        <td width="150">
          <?php echo WP_Topspin_Cron::nextHit(); ?>
        </td>
        <td>
          <form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
          <input type="hidden" name="topspin_post_action" value="reset_cron" />
          <button type="submit" class="button-primary"><?php _e('Reset'); ?></button>
          </form>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label>Next Offers Images Hit:</label></th>
        <td>
          <?php echo WP_Topspin_Cron::nextHitImages(); ?>
        </td>
        <td>
          <form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
          <input type="hidden" name="topspin_post_action" value="reset_cron_images" />
          <button type="submit" class="button-primary"><?php _e('Reset'); ?></button>
          </form>
        </td>
      </tr>
    </tbody>
  </table>

</div>
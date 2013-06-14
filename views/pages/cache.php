<div class="wrap">

  <h2>Database Cache</h2>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row"><label>Sync Artists</label></th>
        <td>
          <form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
          <input type="hidden" name="topspin_post_action" value="sync_artists" />
          <button type="submit" class="button-primary"><?php _e('Sync'); ?></button>
          <span class="description">Last Cached: <?php echo WP_Topspin_Cache::lastCached('artists'); ?> (Automation is not supported.)</span>
          </form>
        </td>
      </tr>
      <?php if(TOPSPIN_HAS_SYNCED_ARTISTS) : ?>
        <tr valign="top">
          <th scope="row"><label>Sync Offers</label></th>
          <td>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
            <input type="hidden" name="topspin_post_action" value="sync_offers" />
            <button type="submit" class="button-primary"><?php _e('Sync'); ?></button>
            <span class="description">Last Cached: <?php echo WP_Topspin_Cache::lastCached('offers'); ?> (Runs hourly)</span>
            </form>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label>Sync Offer Images</label></th>
          <td>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
            <input type="hidden" name="topspin_post_action" value="sync_offers_images" />
            <button type="submit" class="button-primary"><?php _e('Sync'); ?></button>
            <span class="description">Last Cached: <?php echo WP_Topspin_Cache::lastCached('offers_images'); ?> (Runs daily)</span>
            </form>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label>Sync Products</label></th>
          <td>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
            <input type="hidden" name="topspin_post_action" value="sync_products" />
            <button type="submit" class="button-primary"><?php _e('Sync'); ?></button>
            <span class="description">Last Cached: <?php echo WP_Topspin_Cache::lastCached('products'); ?> (Runs hourly)</span>
            </form>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

</div>
<?php
 /**
  * Represents the view for the administration dashboard.
  *
  * This includes the header, options, and other information that should provide
  * The User Interface to the end user.
  *
  * @package   Plugin_Name
  * @author    Your Name <email@example.com>
  * @license   GPL-2.0+
  * @link      http://example.com
  * @copyright 2013 Your Name or Company Name
  */
?>
<div class="wrap">

    <div style="float:left; margin-right: 20px"><img src="<?php echo PLUGIN_ASSETS_URL .'/bing.png' ?>" /></div>
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <form method="post" action="">
        
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="blogname"><?php _e('API KEY title', PLUGIN_TXT_DOMAIN) ?></label></th>
                    <td>
                        <input name="API_KEY" type="text" id="API_KEY" value="<?php echo $data['API_KEY'] ?>" class="regular-text">
                        <p class="description"><?php _e('API KEY description', PLUGIN_TXT_DOMAIN) ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="blogdescription"><?php _e('Cache Expire title', PLUGIN_TXT_DOMAIN) ?></label></th>
                    <td>
                        <input name="cache_expire" type="text" id="cache_expire" value="<?php echo $data['cache_expire'] ?>" class="small-text">
                        <span id="human-time" style="margin-left:10px"></span>
                        <p class="description"><?php _e('Cache Expire description', PLUGIN_TXT_DOMAIN) ?></p>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes') ?>"></p>
    </form>

</div>

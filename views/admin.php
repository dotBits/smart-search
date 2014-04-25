<div class="wrap">

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <form method="post" action="">
        
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="blogname"><?php _e('API KEY title', PLUGIN_TXT_DOMAIN) ?></label></th>
                    <td>
                        <input name="API_KEY" type="text" id="API_KEY" value="<?php echo $data['API_KEY'] ?>" class="regular-text">
                        <p class="description"><?php _e('API KEY description', PLUGIN_TXT_DOMAIN) ?>. <?php _e('BING Plan description', PLUGIN_TXT_DOMAIN) ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="blogdescription"><?php _e('Cache Expire title', PLUGIN_TXT_DOMAIN) ?></label></th>
                    <td>
                        <input name="cache_expire" type="text" id="cache_expire" value="<?php echo $data['cache_expire'] ?>" class="medium-text">
                        <span id="human-time" style="margin-left:10px"></span>
                        <p class="description"><?php _e('Cache Expire description', PLUGIN_TXT_DOMAIN) ?></p>
                    </td>
                </tr>
		<tr valign="top">
                    <th scope="row">
			<?php if(empty($_GET['clear'])) : ?>
			    <label for="blogname"><?php _e('Cache clear', PLUGIN_TXT_DOMAIN) ?></label>
			<?php else : ?>
			    <label for="blogname"><?php _e('Cache cleared', PLUGIN_TXT_DOMAIN) ?></label>
			<?php endif;  ?>
		    </th>
                    <td>
                        <p class="description"><?php _e('Cache clear description', PLUGIN_TXT_DOMAIN) ?>
                    </td>
                </tr>
		
		<tr valign="top">		    
                    <th scope="row"><label for="blogname"><?php _e('Force domain title', PLUGIN_TXT_DOMAIN) ?></label></th>
                    <td>
                        <input name="context_domain" type="text" id="context_domain" value="<?php echo $data['context_domain'] ?>" class="regular-text">
                        <p class="description">
			    <?php echo sprintf(
				__('Force domain context %s', PLUGIN_TXT_DOMAIN), str_replace(array("http://", "https://"), "", site_url())
				) 
			    ?>
			</p>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes') ?>"></p>
    </form>

</div>

<div class="wrap">

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <h4><?php _e("Plugin description admin", PLUGIN_TXT_DOMAIN) ?></h4>

    <form method="post" action="">
        
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="API_KEY"><?php _e('API KEY title', PLUGIN_TXT_DOMAIN) ?></label></th>
                    <td>
                        <input placeholder="<?php _e("API KEY placeholder", PLUGIN_TXT_DOMAIN) ?>" name="API_KEY" type="text" id="API_KEY" value="<?php echo $data['API_KEY'] ?>" class="regular-text">
                        <p class="description"><?php _e('API KEY description', PLUGIN_TXT_DOMAIN) ?>. <?php _e('BING Plan description', PLUGIN_TXT_DOMAIN) ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="cache_expire"><?php _e('Cache Expire title', PLUGIN_TXT_DOMAIN) ?></label></th>
                    <td>
                        <input name="cache_expire" type="text" id="cache_expire" value="<?php echo $data['cache_expire'] ?>" class="medium-text">
                        <span id="human-time" style="margin-left:10px"></span>
                        <p class="description"><?php _e('Cache Expire description', PLUGIN_TXT_DOMAIN) ?></p>
                    </td>
                </tr>
		<tr valign="top">
                    <th scope="row">
			<?php if(empty($_GET['clear'])) : ?>
			    <label><?php _e('Cache clear', PLUGIN_TXT_DOMAIN) ?></label>
			<?php else : ?>
			    <label><?php _e('Cache cleared', PLUGIN_TXT_DOMAIN) ?></label>
			<?php endif;  ?>
		    </th>
                    <td>
                        <p class="description"><?php _e('Cache clear description', PLUGIN_TXT_DOMAIN) ?>
                    </td>
                </tr>
		
		<tr valign="top">		    
                    <th scope="row"><label for="context_domain"><?php _e('Force domain title', PLUGIN_TXT_DOMAIN) ?></label></th>
                    <td>
                        <input placeholder="<?php _e("Context domain placeholder", PLUGIN_TXT_DOMAIN) ?>" name="context_domain" type="text" id="context_domain" value="<?php echo $data['context_domain'] ?>" class="regular-text">
                        <p class="description">
			    <?php echo sprintf(
				__('Force domain context %s', PLUGIN_TXT_DOMAIN), str_replace(array("http://", "https://"), "", site_url())
				) 
			    ?>
			</p>
                    </td>
                </tr>
		
		<tr valign="top">		    
                    <th scope="row"><label for="no_results_url"><?php _e('No results url title', PLUGIN_TXT_DOMAIN) ?></label></th>
                    <td>
                        <input name="no_results_url" type="text" id="no_results_url" value="<?php echo $data['no_results_url'] ?>" class="regular-text" placeholder="<?php _e("No results url placeholder", PLUGIN_TXT_DOMAIN) ?>">
                        <p class="description">
			    <?php _e('No results description', PLUGIN_TXT_DOMAIN) ?>
			</p>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', PLUGIN_TXT_DOMAIN) ?>"></p>
    </form>

</div>

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
	
	<hr>
	
	<h3><?php _e('Display Options', PLUGIN_TXT_DOMAIN) ?></h3>
	<h4><?php _e('Display Options description', PLUGIN_TXT_DOMAIN) ?></h4>
	<table class="form-table">
            <tbody>
		<tr valign="top">
		    <th scope="col"><label for=""></label></th>
		    <th scope="col"><label for=""><?php _e('Text to use', PLUGIN_TXT_DOMAIN) ?></label></th>
		    <th scope="col"><label for=""><?php _e('Highlighted', PLUGIN_TXT_DOMAIN) ?></label></th>
		    <th scope="col"><label for=""><?php _e('Occurrences background color', PLUGIN_TXT_DOMAIN) ?></label></th>
		    <th scope="col"><label for=""><?php _e('Occurrences text color', PLUGIN_TXT_DOMAIN) ?></label></th>
		</tr>
		<tr valign="top">		    
                    <th scope="row"><label for="use_remote_title"><?php _e('Post Title', PLUGIN_TXT_DOMAIN) ?></label></th>
                    <td>
			<?php $selected = $data['use_remote_title'] ? 'selected="selected"' : "" ?>
			<select style="width: 100%" name="use_remote_title">
			    <option value="0" <?php echo $selected ?>><?php _e('use_local_title', PLUGIN_TXT_DOMAIN) ?></option>
			    <option value="1" <?php echo $selected ?>><?php _e('use_remote_title', PLUGIN_TXT_DOMAIN) ?></option>
			</select>
                    </td>
		    <td>
			<?php $selected = $data['highlight_title'] ? 'selected="selected"' : "" ?>
			<select style="width: 100%" name="highlight_title">
			    <option value="0" <?php echo $selected ?>><?php _e('not_highlight_title', PLUGIN_TXT_DOMAIN) ?></option>
			    <option value="1" <?php echo $selected ?>><?php _e('highlight_title_with_color', PLUGIN_TXT_DOMAIN) ?></option>
			</select>
                    </td>
		    <td>
			<input type="text" name="highlight_title_color" value="<?php echo $data['highlight_title_color'] ?>" style="display:block">
                    </td>
		    <td>
			<input type="text" name="highlight_title_txt_color" value="<?php echo $data['highlight_title_txt_color'] ?>" style="display:block">
                    </td>
                </tr>
		
		<tr valign="top">		    
                    <th scope="row"><label for="use_remote_excerpt"><?php _e('Post Excerpt', PLUGIN_TXT_DOMAIN) ?></label></th>
                    <td>
			<?php $selected = $data['use_remote_excerpt'] ? 'selected="selected"' : "" ?>
			<select style="width: 100%" name="use_remote_excerpt">
			    <option value="0" <?php echo $selected ?>><?php _e('use_local_excerpt', PLUGIN_TXT_DOMAIN) ?></option>
			    <option value="1" <?php echo $selected ?>><?php _e('use_remote_excerpt', PLUGIN_TXT_DOMAIN) ?></option>
			</select>
                    </td>
		    <td>
			<?php $selected = $data['highlight_excerpt'] ? 'selected="selected"' : "" ?>
			<select style="width: 100%" name="highlight_excerpt">
			    <option value="0" <?php echo $selected ?>><?php _e('not_highlight_excerpt', PLUGIN_TXT_DOMAIN) ?></option>
			    <option value="1" <?php echo $selected ?>><?php _e('highlight_excerpt_with_color', PLUGIN_TXT_DOMAIN) ?></option>
			</select>
                    </td>
		    <td>
			<input type="text" name="highlight_excerpt_color" value="<?php echo $data['highlight_excerpt_color'] ?>" style="display:block">
                    </td>
		    <td>
			<input type="text" name="highlight_excerpt_txt_color" value="<?php echo $data['highlight_excerpt_txt_color'] ?>" style="display:block">
                    </td>
                </tr>
                
            </tbody>
        </table>
	
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', PLUGIN_TXT_DOMAIN) ?>"></p>
    </form>

</div>

<div id="sunrise-plugin-settings" class="wrap">
	<div id="icon-options-general" class="icon32 hide-if-no-js"><br /></div>
	<h2 id="sunrise-plugin-tabs" class="nav-tab-wrapper hide-if-no-js">
		<?php
			// Show tabs
			$this->render_tabs();
		?>
	</h2>
	<?php
		// Show notifications
		$this->notifications( array(
			'js' => __( 'For full functionality of this page it is reccomended to enable javascript.', PLUGIN_TXT_DOMAIN ),
			'reseted' => __( 'Settings reseted successfully', PLUGIN_TXT_DOMAIN ),
			'not-reseted' => __( 'There is already default settings', PLUGIN_TXT_DOMAIN ),
			'saved' => __( 'Settings saved successfully', PLUGIN_TXT_DOMAIN ),
			'not-saved' => __( 'Settings not saved, because there is no changes', PLUGIN_TXT_DOMAIN )
		) );
	?>
	<form action="<?php echo $this->admin_url; ?>" method="post" id="sunrise-plugin-options-form">
		<?php
			// Show options
			$this->render_panes();
		?>
		<input type="hidden" name="action" value="save" />
	</form>
</div>
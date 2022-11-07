<?php

/**
 * The form to be loaded on the plugin's admin page
 */
if (current_user_can('manage_options')) {
    if (get_option('site_watch_key')) {
        $does_key_exist = true;
        $title = "Site Watch key already generated";
        $paragraph = "<p>You already have a key in place, would your like to generate another?</p><p><span style='color:red;'>Warning!</span> the connection wih Site Watch will be broken until you add your new key in the Site Watch settings</p>";
        $button_text = "Regenerate Site Watch Key";
    } else {
        $does_key_exist = false;
        $title = "Generate Site Watch Key";
        $paragraph = "<p>Click below to generate a key for your WordPress site.</p>";
        $button_text = "Generate Site Watch Key";
    } ?>
		<div class="wrap">
			<h2><?php echo $title ?></h2>
			<?php echo $paragraph ?>
			<div class="sw_add_user_meta_form">
				<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" >
					<input type="hidden" name="action" value="sw_form_response">
					<input type="hidden" name="sw_add_user_meta_nonce" value="<?php echo wp_create_nonce('sw_add_user_meta_form_nonce') ?>" />

					<p><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $button_text ?>"></p>
				</form>

				<div id="sw_form_feedback"></div>
			</div>
		</div>
	<?php
} else {
    ?>
		<p> <?php __("You are not authorized to perform this operation.", $this->plugin_name) ?> </p>
	<?php
}

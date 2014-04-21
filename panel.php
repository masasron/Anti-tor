<?php
$settings_were_changed = false;
if ( isset( $_POST["at_panel_nonce_submit"] ) ) {
	if ( wp_verify_nonce($_POST['at_panel_nonce_submit'], 'at_panel_nonce') ) {
		// Validated Request
		$at_message = isset($_POST["at_message"]) ? $_POST["at_message"] : '';
		
		// Update message option
		update_option("at_message", $at_message);
		
		// Update checkbox state
		if ( !isset($_POST["at_block"]) ) {
			update_option("at_block",false); // Stop blocking tor users
		} else {
			update_option("at_block",true); // Start blocking tor users
		}
		
		// Settings was changed
		$settings_were_changed = true;
	} else {
		// Invalid Request //
		die("Invalid nonce.");
	}
}
$block = get_option("at_block");
?>
<div class="wrap">
	<h2>Anti Tor</h2>
	<?php
	// Display a message if settings were changed
	if ( $settings_were_changed ) {
	?>
	<div id="message" class="updated">
		<p>Settings saved.</p>
	</div><!-- message -->
	<?php
	} // End If
	?>
	<div class="tool-box">
		<form action="" method="POST">
			<label for="at_block" id="at_block_holder">
				<input name="at_block" type="checkbox" id="at_block" value="1"
				<?php
				if ($block == true) {
					echo 'checked="checked"';
				}
				?>
				> Block access for Tor users
			</label>
			<div id="hidden-options" style="<?php if ($block == true) { echo 'display:block;'; } ?>">
				<h4> Block Message <span class="gray">(Optional)</span> </h4>
				<textarea name="at_message" id="at_message" placeholder="Sorry you can't use Tor here..."><?php echo stripslashes( esc_attr( get_option("at_message") ) ); ?></textarea>
				<p class="description">You may use html tags here</p>
			</div><!-- hidden-options -->
			<div class="nonce">
				<?php
				/* Print nonce inputs */
				wp_nonce_field('at_panel_nonce', 'at_panel_nonce_submit');
				?>
			</div><!-- nonce -->
			<?php 
			if ( ANTITOR_BLOCKED_COUNT ) {
			?>
			<h3><span class="big-number"><?php echo ANTITOR_BLOCKED_COUNT; ?></span> <span class="inline">possible attacks were detected & stopped.</span></h3>
			<?php 
			}
			?>
			<p>
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
			</p>
		</form>
	</div><!-- tool-box -->
</div><!-- wrap -->
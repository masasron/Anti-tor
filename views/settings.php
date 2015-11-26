<div class="wrap">
    <h2>Anti Tor Settings</h2>
    <h4><?php echo intval($fields['anti_tor_blocked_count']); ?> Tor Access attempts detected & blocked.</h4>
    <form action="" method="POST">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="anti_tor_message">Custom Blocked Message</label>
                        <p clsss="description">Show tor users a custom message.</p>
                    </th>
                    <td>
                        <input name="anti_tor_message" autocomplete="off" type="text" id="anti_tor_message" value="<?php echo esc_attr($fields['anti_tor_message']) ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
	              <th scope="row">
                        <label for="anti_tor_active">Power Switch</label>
                        <p clsss="description">On Off switch for anti tor.</p>
                    </th>
                    <td>
                        <input name="anti_tor_active" <?php checked($fields['anti_tor_active'],1) ?> type="checkbox" id="anti_tor_message"  value="1">
                    </td>
                </tr>
        </table>
        <div class="submit">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('AntiTorNonce'); ?>" />
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </div>
    </form>
</div>
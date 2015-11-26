<?php

class AntiTorSettingsController extends Controller
{

    /**
     * Anti Tor settings
     * @ver array
     */
    private $settings = array(
        'anti_tor_active',
        'anti_tor_message',
        'anti_tor_blocked_count'
    );

    /**
     * Reset all of the Anti Tor settings
     * @return void
     */
    public function uninstall()
    {
        foreach ($this->settings as $option)
        {
            delete_option($option);
        }
    }

    /**
     * Render the settings view
     * @return string
     */
    public function settings()
    {
        $fields = $this->shortcut->getOptions($this->settings);
        return $this->view('settings', compact('fields'));
    }

    /**
     * Save user settings
     * @param array $request
     * @return void
     */
    public function save(array $request)
    {
        // CSRF validation
        if (!isset($request['nonce']) ||
                !wp_verify_nonce($request['nonce'], 'AntiTorNonce'))
        {
            return $this->shortcut->notice('Oops! invalid request.', 'error');
        }

        $this->shortcut->updateOptions($request, $this->settings);
        
        if (!isset($request['anti_tor_active'])){
	        delete_option('anti_tor_active');
        }
        
        $this->shortcut->notice('Settings were saved.');
    }

}

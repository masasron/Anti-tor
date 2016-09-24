<?php

class AntiTorController extends Controller
{

    /**
     * Detect Tor users
     * @return void
     */
    public function detectTorUsers()
    {
        // Make sure the user want to block tor users
        if (get_option('anti_tor_active') != '1')
        {
            return;
        }

        // Check if current client is a Tor user
        if (!$this->isTorExitNode())
        {
            return;
        }

        // Update blocked count
        $count = intval(get_option('anti_tor_blocked_count'));
        update_option('anti_tor_blocked_count', $count++);

        // Tor user was detected, block access.
        die(esc_attr(get_option('anti_tor_message')));
    }

    /**
     * Detect tor exit node by ip address
     * @return bool
     */
    private function isTorExitNode()
    {
        $serverPort = $this->server['SERVER_PORT'];
        $remoteAddr = $this->reverseIp($this->getClientIp());
        $serverAddr = $this->reverseIp($this->server['SERVER_ADDR']);
        $placeholders = '%s.%s.%s.ip-port.exitlist.torproject.org';
        $name = sprintf($placeholders, $remoteAddr, $serverPort, $serverAddr);
        return ( gethostbyname($name) === '127.0.0.2' );
    }

    /**
     * Get the current client ip address, support cloudflare.
     * @return string
     */
    private function getClientIp()
    {
        if (isset($this->server['HTTP_CF_CONNECTING_IP']))
        {
            return $this->server['HTTP_CF_CONNECTING_IP'];
        }
        return $this->server['REMOTE_ADDR'];
    }

    /**
     * Reverse ip order
     * @param string $ip
     * @return string
     */
    private function reverseIp($ip)
    {
        $ipParts = explode('.', $ip);
        return $ipParts[3] . '.' . $ipParts[2] . '.' .
                $ipParts[1] . '.' . $ipParts[0];
    }

}

<?php
use PleskExt\RealIpAddress\Service;

class Modules_RealIpAddress_WebServer extends pm_Hook_WebServer {
    private $config;

    /**
     * Class constructor
     */
    public function __construct() {
        $this->config = Service::getNginxConfiguration();
    }


    /**
     * @inheritdoc
     */
    public function getDomainNginxConfig(pm_Domain $domain) {
        if (empty($this->config)) return "";
        return "# (Domain configuration) #\n" . $this->config;
    }


    /**
     * @inheritdoc
     */
    public function getDomainNginxProxyConfig(pm_Domain $domain) {
        if (empty($this->config)) return "";
        return "# (Proxy configuration) #\n" . $this->config;
    }


    /**
     * @inheritdoc
     */
    public function getWebmailNginxConfig(pm_Domain $domain, $type) {
        if (empty($this->config)) return "";
        return "# (Webmail configuration) #\n" . $this->config;
    }


    /**
     * @inheritdoc
     */
    public function getForwardingDomainNginxConfig(pm_Domain $domain) {
        if (empty($this->config)) return "";
        return "# (Forwarding configuration) #\n" . $this->config;
    }
}

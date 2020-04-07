<?php
use PleskExt\RealIpAddress\Service;

// Delete settings
pm_Settings::clean();

// Remove configuration from sites
Service::unregister();
Service::apply();

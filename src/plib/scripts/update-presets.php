<?php
use PleskExt\RealIpAddress\Service;

$oldConf = Service::getNginxConfiguration();
Service::updatePresetRanges();
$newConf = Service::getNginxConfiguration();
if ($oldConf !== $newConf) {
    Service::apply();
}

<?php
$messages = [
    'appName' => 'Real IP Address',
    'settings' => 'Settings',
    'preview' => 'Preview',

    'presets' => 'Presets',
    'presetsDesc' => 'If your server is running behind one or more than one of these providers, just check their respective checkboxes to automatically configure and enable them.',
    'enableForPreset' => 'Enable for %%preset%%',
    'customProvider' => 'Custom Provider',
    'customProviderDesc' => 'If your server is running behind a provider not natively supported by this extension, you can manually configure it from here.',
    'enableCustomProvider' => 'Enabled Custom Provider',
    'customIpRanges' => 'Custom IP Ranges',
    'customIpRangesDesc' => 'In CIDR notation, one per line',
    'advancedSettings' => 'Advanced Settings',
    'advancedSettingsDesc' => 'For experienced users only. Do not change any of these settings unless you know what you are doing.',
    'headerField' => 'Header field',
    'headerFieldDesc' => 'Header field containing the real IP address of the client',
    'headerFieldHelp' => 'E.g.: X-Forwarded-For',
    'enableRecursiveMode' => 'Enable recursive mode',
    'enableRecursiveModeDesc' => 'If recursive search is enabled, the original client address that matches one of the trusted addresses is replaced by the last non-trusted address sent in the request header field.',
    'saveAndApply' => 'Save and Apply',

    'changesApplied' => 'Changes successfully applied.',
    'noConfApplied' => 'No configuration is being applied',
    'permDenied' => 'Permission denied',
    'notValidRange' => 'Not a valid list of CIDR ranges.',
    'notValidHeaderField' => 'Not a valid header field.',

    'form.p1' => 'When running your Plesk server behind a proxy, such as a load balancer or a CDN, both access and error logs will not store the actual IP Address of your visitors.',
    'form.p2' => 'Luckily, as this is a very common situation, nginx has a module called "realip" for solving this very problem. You can use this extension to configure the behavior of such module without having to manually write nginx configurations for each domain.',
    'preview.p1' => 'At the moment, the nginx configuration being applied by this extension to all domains in the server is the following:'
];

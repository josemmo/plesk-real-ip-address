<?php
namespace PleskExt\RealIpAddress;

class Settings {
    const PRESETS = [
        'cloudflare' => [
            'name' => 'Cloudflare'
        ],
        'cloudfront' => [
            'name' => 'CloudFront'
        ],
        'fastly' => [
            'name' => 'Fastly'
        ]
    ];
    const DEFAULT_HEADER = "X-Forwarded-For";

    /**
     * Get enabled presets
     * @return string[] List of enabled presets
     */
    public static function getEnabledPresets(): array {
        $value = \pm_Settings::get('enabledPresets');
        return empty($value) ? [] : explode(',', $value);
    }


    /**
     * Set enabled presets
     * @param string[] $enabledPresets List of enabled presets
     */
    public static function setEnabledPresets(array $enabledPresets) {
        $value = empty($enabledPresets) ? null : implode(',', $enabledPresets);
        \pm_Settings::set('enabledPresets', $value);
    }


    /**
     * Get custom provider
     * @return array Custom provider settings
     */
    public static function getCustomProvider(): array {
        $ipRanges = \pm_Settings::get('customProvider_ipRanges');
        return [
            "enabled"  => (bool) \pm_Settings::get('customProvider_enabled'),
            "ipRanges" => empty($ipRanges) ? [] : explode("\n", $ipRanges)
        ];
    }


    /**
     * Set custom provider settings
     * @param boolean       $enabled  Is custom provider enabled
     * @param string[]|null $ipRanges IP ranges
     */
    public static function setCustomProvider(bool $enabled, ?array $ipRanges) {
        \pm_Settings::set('customProvider_enabled',  $enabled         ? "1"  : null);
        \pm_Settings::set('customProvider_ipRanges', empty($ipRanges) ? null : implode("\n", $ipRanges));
    }


    /**
     * Get advanced settings
     * @return array Advanced settings
     */
    public static function getAdvanced(): array {
        return [
            "header"    => \pm_Settings::get('advanced_header', self::DEFAULT_HEADER),
            "recursive" => (bool) \pm_Settings::get('advanced_recursive')
        ];
    }


    /**
     * Set advanced settings
     * @param string  $header    Real IP Address header field
     * @param boolean $recursive Is recursive mode enabled
     */
    public static function setAdvanced(string $header, bool $recursive) {
        \pm_Settings::set('advanced_header',    empty($header) ? null : $header);
        \pm_Settings::set('advanced_recursive', $recursive     ? "1"  : null);
    }
}

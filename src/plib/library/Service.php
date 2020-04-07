<?php
namespace PleskExt\RealIpAddress;

class Service {
    const TASK_FILENAME = "update-presets.php";

    /**
     * Register service
     */
    public static function register() {
        self::unregister();
        $task = new \pm_Scheduler_Task();
        $task->setSchedule(\pm_Scheduler::$EVERY_DAY);
        $task->setCmd(self::TASK_FILENAME);
        \pm_Scheduler::getInstance()->putTask($task);
    }


    /**
     * Unregister service
     */
    public static function unregister() {
        $scheduler = \pm_Scheduler::getInstance();
        foreach ($scheduler->listTasks() as $task) {
            if ($task->getCmd() == self::TASK_FILENAME) {
                $scheduler->removeTask($task);
                break;
            }
        }
    }


    /**
     * Get nginx configuration
     * @return string nginx configuration
     */
    public static function getNginxConfiguration(): string {
        $res = "";

        // Add presets
        foreach (Settings::getEnabledPresets() as $preset) {
            $res .= "# " . Settings::PRESETS[$preset]['name'] . " settings\n";
            foreach (self::getPresetIpRanges($preset) as $range) {
                $res .= "set_real_ip_from $range;\n";
            }
            $res .= "\n";
        }

        // Add custom provider
        $customProvider = Settings::getCustomProvider();
        if ($customProvider['enabled']) {
            $res .= "# Custom provider settings\n";
            foreach ($customProvider['ipRanges'] as $range) {
                $res .= "set_real_ip_from $range;\n";
            }
            $res .= "\n";
        }

        // Add advanced settings
        if (!empty($res)) {
            $advanced = Settings::getAdvanced();
            $res .= "# Advanced settings\n";
            $res .= "real_ip_header " . $advanced['header'] . ";\n";
            $res .= "real_ip_recursive " . ($advanced['recursive'] ? 'on' : 'off') . ";\n";
        }

        return $res;
    }


    /**
     * Apply configuration
     */
    public static function apply() {
        $manager = new \pm_WebServer();
        $domains = \pm_Domain::getAllDomains();
        foreach ($domains as $domain) {
            $manager->updateDomainConfiguration($domain);
        }
    }


    /**
     * Update preset ranges
     */
    public static function updatePresetRanges() {
        foreach (Settings::getEnabledPresets() as $preset) {
            self::getPresetIpRanges($preset, true);
            \pm_Log::info('Updated IP ranges for ' . Settings::PRESETS[$preset]['name']);
        }
    }


    /**
     * Get preset IP ranges
     * @param  string   $preset      Preset
     * @param  boolean  $forceUpdate Force update of ranges
     * @return string[]              IP ranges
     */
    private static function getPresetIpRanges(string $preset, $forceUpdate=false): array {
        // Get ranges from cache, if available
        if (!$forceUpdate) {
            $cache = \pm_Settings::get("cache_$preset");
            if (!empty($cache)) {
                return explode("\n", $cache);
            }
        }

        // Define variable to store downloaded ranges
        $ranges = [];
        switch ($preset) {
            case 'cloudflare':
                $ranges = RangesFetcher::cloudflare();
                break;
            case 'cloudfront':
                $ranges = RangesFetcher::cloudfront();
                break;
            case 'fastly':
                $ranges = RangesFetcher::fastly();
                break;
        }

        // Save and return ranges
        \pm_Settings::set("cache_$preset", empty($ranges) ? null : implode("\n", $ranges));
        return $ranges;
    }
}

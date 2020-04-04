<?php
namespace PleskExt\RealIpAddress;

class CidrUtils {
    /**
     * Validate CIDR ranges
     * @param  string  $ranges CIDR ranges
     * @return boolean         Is valid
     */
    public static function validate(string $ranges): bool {
        return !empty(self::parse($ranges));
    }


    /**
     * Parse CIDR ranges
     * @param  string        $ranges CIDR ranges
     * @return string[]|null         Array of parsed ranges or NULL for not valid
     */
    public static function parse(string $ranges): ?array {
        $lines = explode("\n", str_replace("\r", '', $ranges));
        foreach ($lines as &$line) {
            $line = trim($line);
            if (!self::validateSingleCidr($line)) {
                return null;
            }
        }
        return $lines;
    }


    /**
     * Validate single CIDR
     * @param  string  $cidr CIDR range
     * @return boolean       Is valid
     */
    public static function validateSingleCidr(string $cidr): bool {
        list($ip, $netmask) = explode('/', $cidr, 2);

        // Preliminary netmask validation
        if (!preg_match('/^[0-9]+$/', $netmask)) {
            return false;
        }
        $netmask = (int) $netmask;

        // Validate IP address and netmask range
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return ($netmask <= 32);
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return ($netmask <= 128);
        }

        return false;
    }
}

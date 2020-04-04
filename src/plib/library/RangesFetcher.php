<?php
namespace PleskExt\RealIpAddress;

class RangesFetcher {
    /**
     * Fetch Cloudflare ranges
     * @return string[] List of IP ranges
     */
    public static function cloudflare(): array {
        $ranges = [];
        foreach (['v4', 'v6'] as $version) {
            $res = self::getUrl("https://www.cloudflare.com/ips-$version") ?? "";
            $res = CidrUtils::parse(trim($res));
            if (!empty($res)) {
                $ranges = array_merge($ranges, $res);
            }
        }
        return $ranges;
    }


    /**
     * Fetch CloudFront ranges
     * @return string[] List of IP ranges
     */
    public static function cloudfront(): array {
        // Fetch and parse data
        $res = self::getUrl('https://d7uri8nf7uskq.cloudfront.net/tools/list-cloudfront-ips');
        $res = json_decode($res, true);
        $res = array_merge(
            $res['CLOUDFRONT_GLOBAL_IP_LIST'] ?? [],
            $res['CLOUDFRONT_REGIONAL_EDGE_IP_LIST'] ?? [],
        );

        // Filter out invalid ranges
        $ranges = [];
        foreach ($res as $range) {
            if (CidrUtils::validateSingleCidr($range)) {
                $ranges[] = $range;
            }
        }

        return $ranges;
    }


    /**
     * Fetch Fastly ranges
     * @return string[] List of IP ranges
     */
    public static function fastly(): array {
        // Fetch and parse data
        $res = self::getUrl('https://api.fastly.com/public-ip-list');
        $res = json_decode($res, true);
        $res = array_merge(
            $res['addresses'] ?? [],
            $res['ipv6_addresses'] ?? [],
        );

        // Filter out invalid ranges
        $ranges = [];
        foreach ($res as $range) {
            if (CidrUtils::validateSingleCidr($range)) {
                $ranges[] = $range;
            }
        }

        return $ranges;
    }


    /**
     * Get URL
     * @param  string      $url Request URL
     * @return string|null      Response or NULL in case of error
     */
    private static function getUrl(string $url): ?string {
        try {
            $client = new \Zend_Http_Client($url);
            return $client->request(\Zend_Http_Client::GET)->getBody();
        } catch (\Exception $e) {
            \pm_Log::debug($e);
            \pm_Log::err("Failed to get contents from url '$url': {$e->getMessage()}");
        }
        return null;
    }
}

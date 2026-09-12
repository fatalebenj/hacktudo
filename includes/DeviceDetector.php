<?php
/**
 * DeviceDetector.php
 *
 * Detecta se o dispositivo que acessa o site e desktop ou mobile,
 * utilizando a biblioteca foroco/php-browser-detection.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use foroco\BrowserDetection;

class DeviceDetector
{
    private $browser;
    private $userAgent;

    public function __construct($userAgent = null)
    {
        $this->browser = new BrowserDetection();
        $this->userAgent = $userAgent !== null ? $userAgent : ($_SERVER['HTTP_USER_AGENT'] ?? '');
    }

    public function getDeviceType()
    {
        $osResult = $this->browser->getOS($this->userAgent);
        if (is_array($osResult) && isset($osResult['os_type'])) {
            $osType = strtolower((string) $osResult['os_type']);

            if (in_array($osType, ['mobile', 'mixed'], true)) {
                return 'mobile';
            }

            if ($osType === 'desktop') {
                return 'desktop';
            }
        }

        $deviceResult = $this->browser->getDevice($this->userAgent);
        if (is_array($deviceResult) && isset($deviceResult['device_type'])) {
            $deviceType = strtolower((string) $deviceResult['device_type']);

            if (in_array($deviceType, ['smartphone', 'tablet', 'mobile'], true)) {
                return 'mobile';
            }

            if ($deviceType === 'desktop') {
                return 'desktop';
            }
        }

        $ua = strtolower($this->userAgent);
        if (preg_match('/android|iphone|ipad|ipod|mobile|phone/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }
}

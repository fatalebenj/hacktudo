<?php
/**
 * DeviceDetector.php
 *
 * Responsavel por detectar se o dispositivo que acessa o site
 * eh um Desktop ou um Mobile, usando a biblioteca foroco/browser-detection.
 *
 * Documentacao da lib: https://github.com/foroco/php-device-detector
 */

require_once __DIR__ . '/../vendor/autoload.php';

use foroco\BrowserDetection;

class DeviceDetector
{
    private BrowserDetection $Browser;
    private string $useragent;
    private array $osInfo;

    public function __construct()
    {
        $this->Browser   = new BrowserDetection();
        $this->useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $this->osInfo    = $this->Browser->getOS($this->useragent);
    }

    /**
     * Retorna 'mobile' ou 'desktop' de acordo com o resultado da lib foroco.
     *
     * O array retornado por getOS() traz, entre outras chaves,
     * 'device_type', que pode ser: desktop, smartphone, tablet, tv, etc.
     */
    public function getDeviceType(): string
    {
        $deviceType = $this->osInfo['device_type'] ?? 'desktop';

        // Trata tablet e smartphone como "mobile" para fins de tela de login
        if (in_array($deviceType, ['smartphone', 'tablet'], true)) {
            return 'mobile';
        }

        return 'desktop';
    }

    public function isMobile(): bool
    {
        return $this->getDeviceType() === 'mobile';
    }

    public function isDesktop(): bool
    {
        return $this->getDeviceType() === 'desktop';
    }

    /** Dados brutos retornados pela lib, caso precise depurar. */
    public function getRawOsInfo(): array
    {
        return $this->osInfo;
    }

    public function getUserAgent(): string
    {
        return $this->useragent;
    }
}

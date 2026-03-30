<?php
namespace App\Models;

/**
 * Settings Model - Site configuration from CMS
 */
class Settings
{
    private static ?array $cache = null;

    /**
     * Get all settings
     */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $filePath = DATA_PATH . 'settings.json';
        
        if (!file_exists($filePath)) {
            return self::getDefaults();
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);

        if (!$data) {
            return self::getDefaults();
        }

        self::$cache = $data;
        return $data;
    }

    /**
     * Get single setting value
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::all();
        return $settings[$key] ?? $default;
    }

    /**
     * Get default settings
     */
    private static function getDefaults(): array
    {
        return [
            'siteName' => 'NGUYEN THANH GALLERIE',
            'contactEmail' => 'nguyenthanhgallerie@gmail.com',
            'contactEmail2' => 'tnguyentrangartist78@gmail.com',
            'contactPhone1' => '+84 (028) 3823 8754',
            'contactPhone2' => '+84 (0) 919 268 83',
            'contactAddress' => '139 Dong Khoi Street, Sai Gon Ward, Ho Chi Minh City, Vietnam',
            'openingHours' => "Monday – Sunday\n9:00 AM – 7:00 PM",
            'socialLinks' => [
                'email' => 'thanhart2000@yahoo.com',
                'mapUrl' => 'https://www.google.com/maps/search/139+Dong+Khoi+Street+Sai+Gon+Ward+Ho+Chi+Minh+City+Vietnam'
            ]
        ];
    }

    /**
     * Clear cache (call after settings update)
     */
    public static function clearCache(): void
    {
        self::$cache = null;
    }
}

<?php

namespace WpMailCatcher\Models;

class Settings
{
    static private $settings = null;
    static private $optionsName = 'mailcatcher_settings';
    static private $defaultSettings = [
        'default_view_role' => 'manage_options',
        'default_settings_role' => 'manage_options',
        'auto_delete' => true,
        'timescale' => 'monthly'
    ];

    static public function get($key = null)
    {
        if (self::$settings == null) {
            self::$settings = unserialize(get_option(self::$optionsName, null));
        }

        if (self::$settings == null) {
            self::installOptions();
        }

        if ($key != null && isset(self::$settings[$key])) {
            return self::$settings[$key];
        }

        return self::$settings;
    }

    static public function update($newValues)
    {
        $settings = self::get();

        foreach ($newValues as $key => $newValue) {
            $settings[$key] = $newValue;
        }

        self::$settings = $settings;
        return update_option(self::$optionsName, serialize($settings));
    }

    static public function installOptions()
    {
        if (get_option(self::$optionsName, false) == false) {
            add_option(self::$optionsName, serialize(self::$defaultSettings), '', 'no');
            self::$settings = self::$defaultSettings;
        }
    }

    public static function uninstallOptions()
    {
        delete_option(self::$optionsName);
    }
}

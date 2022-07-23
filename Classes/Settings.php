<?php
/**
 *
 * @class Settings
 * @author Georgy Polyan <acidlord@yandex.ru>
 *
 */
class Settings
{
    static private $log;
    static public function get($settingCode)
    {
        if (!isset(self::$log))
        {
            self::$log = new Log('Classes - Settings.log');
        }
        $sql = 'select * from settings where code = "' . $settingCode . '"';
        //self::$log->write(__LINE__ . ' sql - ' . $sql);
        $setting = Db::exec_query_array($sql);
        //self::$log->write(__LINE__ . ' setting - ' . json_encode($setting, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $setting;
    }
}
?>
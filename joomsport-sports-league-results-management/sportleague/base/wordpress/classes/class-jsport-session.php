<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportSession
{
    public static function get($name)
    {
        if (isset($_SESSION[$name])) {
            return sanitize_text_field(wp_unslash($_SESSION[$name]));
        } else {
            return false;
        }
    }
    public static function set($name, $var)
    {

        $_SESSION[$name] = $var;
    }
}

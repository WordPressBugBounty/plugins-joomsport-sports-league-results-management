<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportText
{
    public static function getFormatedText($text)
    {
        //$text = apply_filters('the_content', $text);
        $text = nl2br($text);
        return $text;
    }
}

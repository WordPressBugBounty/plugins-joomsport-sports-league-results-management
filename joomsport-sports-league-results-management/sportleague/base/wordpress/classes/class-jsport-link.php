<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportLink
{
    public static function season($text, $season_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        
        $link = get_permalink($season_id);
        if ($onlylink) {
            return $link;
        }

        $additAttr = apply_filters("joomsport_link_attr", "", "season", $season_id);

        return '<a href="'.esc_attr($link).'"'.$additAttr.'>'.wp_kses_post($text).'</a>';
    }
    public static function calendar($text, $season_id, $onlylink = false, $Itemid = '', $linkable = true, $params = null)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        $link = get_permalink($season_id);
        $link = add_query_arg( 'action', 'calendar', $link );
        if($params && count($params)){
            foreach ($params as $par) {
               $link = add_query_arg( $par["name"], $par["value"], $link );
            }
        }
        if ($onlylink) {
            return $link;
        }

        $additAttr = apply_filters("joomsport_link_attr", "", "calendar", $season_id);

        return '<a href="'.esc_attr($link).'"'.$additAttr.'>'.wp_kses_post($text).'</a>';
    }
    public static function tournament($text, $tournament_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

    }
    public static function team($text, $team_id, $season_id = 0, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        $link = get_permalink($team_id);
        if($season_id){
            $link = add_query_arg( 'sid', $season_id, $link );
        }
        if ($onlylink) {
            return $link;
        }
        $additAttr = apply_filters("joomsport_link_attr", "", "team", $team_id);

        return '<a href="'.esc_attr($link).'"'.$additAttr.'>'.wp_kses_post($text).'</a>';
    }
    public static function match($text, $match_id, $onlylink = false, $class = '', $Itemid = '', $linkable = true)
    {
        if($match_id){
            if (!$Itemid) {
                $Itemid = self::getItemId();
            }

            $pp = get_post($match_id);
            if (isset($pp->post_status) && $pp->post_status != 'publish' || get_post_status($match_id) == 'private') {
                return $text;
            }
            $link = get_permalink($match_id);
            $link = apply_filters('joomsport_match_link_filter', $link, $match_id);
            if ($onlylink) {
                return $link;
            }
            $additAttr = apply_filters("joomsport_link_attr", "", "match", $match_id);

            return '<a class="'.esc_attr($class).'" href="'.esc_attr($link).'"'.$additAttr.'>'.wp_kses_post($text).'</a>';
        }
    }
    public static function player($text, $player_id, $season_id = 0, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        $link = get_permalink($player_id);
        if($season_id){
            $link = add_query_arg( 'sid', $season_id, $link );
        }
        if ($onlylink) {
            return $link;
        }

        $additAttr = apply_filters("joomsport_link_attr", "", "player", $player_id);

        return '<a href="'.esc_attr($link).'"'.$additAttr.'>'.wp_kses_post($text).'</a>';
    }
    public static function person($text, $player_id, $season_id = 0, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        $link = get_permalink($player_id);
        if($season_id){
            $link = add_query_arg( 'sid', $season_id, $link );
        }
        if ($onlylink) {
            return $link;
        }

        $additAttr = apply_filters("joomsport_link_attr", "", "person", $player_id);

        return '<a href="'.esc_attr($link).'"'.$additAttr.'>'.wp_kses_post($text).'</a>';
    }
    public static function matchday($text, $matchday_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

    }
    public static function venue($text, $venue_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        $link = get_permalink($venue_id);
        if ($onlylink) {
            return $link;
        }
        $additAttr = apply_filters("joomsport_link_attr", "", "venue", $venue_id);

        $enbl_link = JoomsportSettings::get('unbl_venue_link',1);
        /*if(!$enbl_link) {
            $hTeams = JoomsportSettings::get('yteams', array());
            if ($hTeams && is_array($hTeams)) {

                foreach ($hTeams as $hT) {
                    $hVID = get_post_meta($hT, '_joomsport_team_venue', true);
                    if ($hVID == $venue_id) {
                        $enbl_link = true;
                    }
                }


            }
        }*/
        if(!$enbl_link){
            return $text;
        }

        return '<a href="'.esc_attr($link).'"'.$additAttr.'>'.wp_kses_post($text).'</a>';
    }
    public static function club($text, $club_id, $season_id = 0, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        $link =  get_term_link($club_id);
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.esc_attr($link).'">'.wp_kses_post($text).'</a>';
    }
    public static function playerlist($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        $link = get_permalink($season_id);
        $link = add_query_arg( 'action', 'playerlist', $link );
        $link .= $params;
        return $link;
    }
    public static function teamlist($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
    }
    public static function seasonlist($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

    }
    public static function joinseason($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

    }
    public static function jointeam($season_id, $team_id, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
    }
    public static function getItemId()
    {

        return 0;
    }
}

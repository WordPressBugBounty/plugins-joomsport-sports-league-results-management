<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

class JoomsportSettings {
    public static $_config = null;
    
    private function __construct() {
        
    }
    
    public static function getInstance(){

        global $wpdb;
        if(!isset($wpdb->joomsport_config)){
            
            $wpdb->joomsport_config = $wpdb->prefix . 'joomsport_config';
        }
        $allconfig = $wpdb->get_results("SELECT * FROM {$wpdb->joomsport_config}");
        self::$_config = array();
        for($intA=0;$intA<count($allconfig);$intA++){
            if($allconfig[$intA]->cValue){
                self::$_config = array_merge(self::$_config, json_decode($allconfig[$intA]->cValue,true));
            }
        }
    }

    public static function get($val, $default = 0){
        if(!isset(self::$_config)){
            self::getInstance();
        }
        if(isset(self::$_config[$val])){
            return self::$_config[$val];
        }else{
            return $default;
        }
    }
    
    public static function getStandingColumns(){
         $array =  array(
            'played_chk' => array('label' => _x('Played','Standings column', 'joomsport-sports-league-results-management'), 'short' => _x('Pl', 'Played column short name', 'joomsport-sports-league-results-management')),
            'win_chk' => array('label' => __('Won', 'joomsport-sports-league-results-management'), 'short' => _x('W', 'Won column short name','joomsport-sports-league-results-management')),
             'draw_chk' => array('label' => __('Drawn', 'joomsport-sports-league-results-management'), 'short' => _x('D','Drawn column short name', 'joomsport-sports-league-results-management')),
             'lost_chk' => array('label' => __('Lost', 'joomsport-sports-league-results-management'), 'short' => _x('L','Lost column short name', 'joomsport-sports-league-results-management')),
            'otwin_chk' => array('label' => __('ET Won', 'joomsport-sports-league-results-management'), 'short' => _x('W ET','ET Won column short name', 'joomsport-sports-league-results-management')),
            'otlost_chk' => array('label' => __('ET Lost', 'joomsport-sports-league-results-management'), 'short' => _x('L ET','ET Lost column short name', 'joomsport-sports-league-results-management')),
             'wintot_chk' => array('label' => __('Total Won', 'joomsport-sports-league-results-management'), 'short' => _x('TW', 'Total Won column short name','joomsport-sports-league-results-management')),
             'losttot_chk' => array('label' => __('Total Lost', 'joomsport-sports-league-results-management'), 'short' => _x('TL','Total Lost column short name', 'joomsport-sports-league-results-management')),
             'diff_chk' => array('label' => __('Differential', 'joomsport-sports-league-results-management'), 'short' => _x('Diff','Differential column short name', 'joomsport-sports-league-results-management')),
            'gd_chk' => array('label' => __('Goal difference', 'joomsport-sports-league-results-management'), 'short' => _x('GD','Goal difference column short name', 'joomsport-sports-league-results-management')),
            'point_chk' => array('label' => __('Points', 'joomsport-sports-league-results-management'), 'short' => _x('Pts','Points column short name', 'joomsport-sports-league-results-management')),
            'percent_chk' => array('label' => __('Win percent', 'joomsport-sports-league-results-management'), 'short' => _x('WPCT','Win percent column short name', 'joomsport-sports-league-results-management')),
             'totalpercent_chk' => array('label' => __('Total Win percent', 'joomsport-sports-league-results-management'), 'short' => _x('TWPCT','Total Win percent column short name', 'joomsport-sports-league-results-management')),
             'goalscore_chk' => array('label' => __('For', 'joomsport-sports-league-results-management'), 'short' => _x('GF','For column short name', 'joomsport-sports-league-results-management')),
            'goalconc_chk' => array('label' => __('Against', 'joomsport-sports-league-results-management'), 'short' => _x('GA','Against column short name', 'joomsport-sports-league-results-management')),
            'winhome_chk' => array('label' => __('Won home', 'joomsport-sports-league-results-management'), 'short' => _x('WH','Won home column short name', 'joomsport-sports-league-results-management')),
            'winaway_chk' => array('label' => __('Won away', 'joomsport-sports-league-results-management'), 'short' => _x('WA','Won away column short name', 'joomsport-sports-league-results-management')),
            'drawhome_chk' => array('label' => __('Drawn home', 'joomsport-sports-league-results-management'), 'short' => _x('DH','Drawn home column short name', 'joomsport-sports-league-results-management')),
            'drawaway_chk' => array('label' => __('Drawn away', 'joomsport-sports-league-results-management'), 'short' => _x('DA','Drawn away column short name', 'joomsport-sports-league-results-management')),
            'losthome_chk' => array('label' => __('Lost home', 'joomsport-sports-league-results-management'), 'short' => _x('LH','Lost home column short name', 'joomsport-sports-league-results-management')),
            'lostaway_chk' => array('label' => __('Lost away', 'joomsport-sports-league-results-management'), 'short' => _x('LA','Lost away column short name', 'joomsport-sports-league-results-management')),
            'pointshome_chk' =>array('label' => __('Points home', 'joomsport-sports-league-results-management'), 'short' => _x('Pts H','Points home column short name', 'joomsport-sports-league-results-management')),
            'pointsaway_chk' => array('label' => __('Points away', 'joomsport-sports-league-results-management'), 'short' => _x('Pts A','Points away column short name', 'joomsport-sports-league-results-management')),
            'grwin_chk' => array('label' => __('Won in group', 'joomsport-sports-league-results-management'), 'short' => _x('W Grp','Won in group column short name', 'joomsport-sports-league-results-management')),
            'grlost_chk' => array('label' => __('Lost in group', 'joomsport-sports-league-results-management'), 'short' => _x('L Grp','Lost in group column short name', 'joomsport-sports-league-results-management')),
            'grwinpr_chk' => array('label' => __('Win percent in group', 'joomsport-sports-league-results-management'), 'short' => _x('WPCT Grp','Win percent in group column short name', 'joomsport-sports-league-results-management')),
            'curform_chk' => array('label' => __('Current form', 'joomsport-sports-league-results-management'), 'short' => _x('Current form','Current form column short name', 'joomsport-sports-league-results-management')),
            'nextmatch_chk' => array('label' => __('Next Match', 'joomsport-sports-league-results-management'), 'short' => _x('Next Match','Current form column short name', 'joomsport-sports-league-results-management'))

         );

        return $array;
    }
    public static function getKsesSelect(){
        return array("select" => array("name" => array(), "id" => array(), "class" => array(), "onchange" => array(), "onclick" => array(), "multiple" => array(), "data-placeholder" => array()), "option" => array("value" => array(), "selected" => array()), "optgroup" => array("label" => array()));
    }
    public static function getKsesRadio(){
        return array("p" => array("class" => array()),"label" => array("for" => array(), "id" => array(), "class" => array()), "input" => array("value" => array(), "type" => array(), "id" => array(), "name" => array(), "onchange" => array(), "onclick" => array(), "style" => array(), "checked" => array()), "span" => array());
    }
    public static function getKsesEFEdit(){
        return array("select" => array("name" => array(), "id" => array(), "class" => array(), "onchange" => array(), "onclick" => array(), "multiple" => array(), "data-placeholder" => array()), "option" => array("value" => array(), "selected" => array()), "optgroup" => array("label" => array()),"p" => array("class" => array()),"label" => array("for" => array(), "id" => array(), "class" => array()), "input" => array("value" => array(), "type" => array(), "id" => array(), "name" => array(), "onchange" => array(), "onclick" => array(), "style" => array(), "checked" => array()), "span" => array());
    }

    public static function getSortArray(){
        $array = array(
            0 => __("No", "joomsport-sports-league-results-management"),
            1 => __("Points", "joomsport-sports-league-results-management"),
            2 => __("Win Percent", "joomsport-sports-league-results-management"),
            4 => __("Goal difference", "joomsport-sports-league-results-management"),
            5 => __("Goal scored", "joomsport-sports-league-results-management"),
            6 => __("Played", "joomsport-sports-league-results-management"),
            7 => __("Win games", "joomsport-sports-league-results-management"),
            10 => __("Away goals scored", "joomsport-sports-league-results-management"),
            11 => __("Away win games", "joomsport-sports-league-results-management"),
            12 => __("Total Win Percent", "joomsport-sports-league-results-management"),
            100 => __("Head-to-head points", "joomsport-sports-league-results-management"),
            101 => __("Head-to-head away goals scored", "joomsport-sports-league-results-management"),
            102 => __("Head-to-head goals difference", "joomsport-sports-league-results-management"),
            103 => __("Head-to-head win games", "joomsport-sports-league-results-management"),
            104 => __("Head-to-head goals scored", "joomsport-sports-league-results-management"),

        );
        return $array;
    }

    public static function loadSportTemplates(){
        global $wpdb;
        $teamplateClass = $wpdb->get_col("SELECT st.sportTemplateClass FROM {$wpdb->joomsport_sports} as s "
                ." JOIN {$wpdb->joomsport_sports_template} as st ON s.sportTemplateID=st.sportTemplateID");

        for($intA=0;$intA<count($teamplateClass);$intA++){
            if(is_file(JOOMSPORT_PATH_INCLUDES.'classes'.DIRECTORY_SEPARATOR.'sports'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$teamplateClass[$intA].".php")){
                require_once JOOMSPORT_PATH_INCLUDES.'classes'.DIRECTORY_SEPARATOR.'sports'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$teamplateClass[$intA].".php";
            }
        }
    }
}

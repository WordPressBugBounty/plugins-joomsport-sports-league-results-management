<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportgetmdays
{
    public static function getMdays($options)
    {
        if ($options) {
            extract($options);
        }

        if (!isset($ordering)) {
            $ordering = 'md.m_name, md.id';
        }
        
        
        if (isset($season_id) && $season_id) {
            $mdays = array();
            //$tx = jsHelperTermMatchday::getInstance();
            $tx = JoomsportTermsMeta::getTerms('joomsport_matchday', array("hide_empty" => false), array('season_id' => $season_id));

            if(count($tx)){
                foreach ($tx as $mday){
                    $TXmatchday_type = get_term_meta($mday->term_id,'matchday_type', true);
                    $TXis_playoff = get_term_meta($mday->term_id,'is_playoff', true);
                    if((isset($mday_type) && $TXmatchday_type == $mday_type)
                        || !isset($mday_type)){
                        if((isset($is_playoff) && $TXis_playoff == $is_playoff)
                            || !isset($is_playoff)){
                            $tmp = new stdClass();
                            $tmp->id = $mday->term_id;
                            $tmp->m_name = $mday->name;
                            $mdays[] = $tmp;
                        }
                    }


                }
            }

            
            
            

            return $mdays;
        }
    }
    public static function getMdayArray($seasonID){
        $mdaysArray = array();
        $mdoptions['season_id'] = $seasonID;
        $mdoptions['ordering'] = 'md.ordering, md.m_name, md.id';
        $mdays = classJsportgetmdays::getMdays($mdoptions);
        for($intA=0; $intA < count($mdays); $intA++){
            $mdaysArray[] =  $mdays[$intA]->id;
        }
        return $mdaysArray;
    }
    public static function getNext($seasonID, $mdId){
        $res = 0;
        $mdays = classJsportgetmdays::getMdayArray($seasonID);
        if(isset($mdays[0])){
            $key = (int) array_search($mdId, $mdays);
            if(isset($mdays[$key+1])){
                $res = $mdays[$key+1];
            }
        }
        return $res;
    }
    public static function getPrev($seasonID, $mdId){
        $res = 0;
        $mdays = classJsportgetmdays::getMdayArray($seasonID);
        if(isset($mdays[0])){
            $key = (int) array_search($mdId, $mdays);
            if(isset($mdays[$key-1])){
                $res = $mdays[$key-1];
            }
        }
        return $res;
    }
}

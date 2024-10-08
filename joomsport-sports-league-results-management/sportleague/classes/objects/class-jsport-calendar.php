<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-season.php';

class classJsportCalendar
{
    private $season_id = null;
    private $object = null;
    public $lists = null;
    public $view = null;

    public function __construct($season_id = null)
    {

        $this->season_id = $season_id;
        if (!$this->season_id) {
            $this->season_id = get_the_ID();
        }
        if (!$this->season_id) {
            die('ERROR! SEASON ID not DEFINED');
        }
        $season_array['season_id'] = array();
        $seasonObj = new classJsportSeason($this->season_id, false);

        if($seasonObj->isComplex() == '1'){
            $childrens = $seasonObj->getSeasonChildrens();
            if(count($childrens)){
                foreach($childrens as $ch){
                    array_push($season_array['season_id'], $ch->ID);
                }
            }
        } else{
            $season_array['season_id'] = $this->season_id;

        } 

        $childObj = $seasonObj->getChild();
        $this->object = $childObj->getCalendar($season_array);
        $this->lists = $childObj->lists;
        $this->lists['t_single'] = $seasonObj->getSingle();
        $this->lists['pagination'] = $childObj->pagination;
        $this->view = $childObj->getCalendarView();

        $this->setHeaderOptions();

    }

    public function getObject()
    {
        return $this->object;
    }

    public function getRow()
    {
        return $this->getObject();
    }
    public function getView()
    {
        return $this->view;
    }

    public function setHeaderOptions()
    {

        $this->lists['options']['standings'] = $this->season_id;
        if (!$this->lists['t_single'] && JoomsportSettings::get('enbl_linktoplayerlistcal',1) == '1') {
            $this->lists['options']['playerlist'] = $this->season_id;
        }
        $this->lists['options']['print'] = '<a href="javascript:void(0);" onclick="componentPopup();"><span class="glyphicon glyphicon-print"></span></a>';
    }
}

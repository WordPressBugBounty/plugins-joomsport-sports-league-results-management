<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_ENV_CLASSES.'class-jsport-user.php';
class modelJsportTeam
{
    public $season_id = null;
    public $team_id = null;
    public $lists = null;
    public $row = null;

    public function __construct($id, $season_id = 0)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->team_id = (int) classJsportRequest::get('tid');
        } else {
            $this->season_id = $season_id;
            $this->team_id = $id;
        }
        if (!$this->team_id) {
            die('ERROR! Team ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        $teams = jsHelperAllTeamPosts::getInstance();
        if(isset($teams[intval($this->team_id)])){
            $this->row = $teams[intval($this->team_id)];
        }

    }
    public function getRow()
    {
        return $this->row;
    }
    public function loadLists()
    {
        $metadata = get_post_meta($this->team_id,'_joomsport_team_personal',true);
        
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->team_id, '1', $this->season_id);
        if(isset($metadata['middle_name']) && ($metadata['middle_name'])){
            $tmparr = array(__('Middle size name','joomsport-sports-league-results-management') => $metadata['middle_name']);
            $this->lists['ef'] = array_merge($tmparr, $this->lists['ef']);
        }
        if(isset($metadata['short_name']) && ($metadata['short_name'])){
            $tmparr = array(__('Short name','joomsport-sports-league-results-management') => $metadata['short_name']);
            $this->lists['ef'] = array_merge($tmparr, $this->lists['ef']);
        }
        
        
        $this->getPhotos();
        $this->getDefaultImage();
        $this->getHeaderSelect();
        $this->lists['enbl_join'] = $this->canJoinTeam();
        //$this->getCurrentPosition();
        return $this->lists;
    }

    public function getDefaultImage()
    {

        $this->lists['def_img'] = null;
        if (isset($this->lists['photos'][0])) {
            $this->lists['def_img'] = $this->lists['photos'][0];
        }
    }
    public function getPhotos()
    {
        $photos = get_post_meta($this->team_id,'vdw_gallery_id',true);

        $this->lists['photos'] = array();
        if (is_array($photos) && count($photos)) {
            foreach ($photos as $photo) {
                $image_arr = wp_get_attachment_image_src($photo, 'joomsport-thmb-medium');
                if (isset($image_arr[0])) {
                    $this->lists['photos'][] = array("id" => $photo, "src" => $image_arr[0]);
                }
            }
        }
        
    }
    public function getHeaderSelect()
    {
        $javascript = " onchange='fSubmitwTab(this);'";
        $seasons = JoomSportHelperObjects::getParticipiantSeasons($this->team_id);
        $jqre =  JoomSportHelperSelectBox::Optgroup('sid', $seasons,$this->season_id, $javascript.' class="selectpicker"');
        
        $this->lists['tourn'] = $jqre;
    }

    public function getCurrentPosition()
    {
        global $jsDatabase;

        if ($this->season_id) {
            $query = 'SELECT * FROM '.DB_TBL_SEASON_TABLE.' '
                .' WHERE season_id = '.$this->season_id
                .' AND participant_id = '.$this->team_id
                .' ORDER BY ordering';

            return $jsDatabase->selectObject($query);
        }

        return '';
    }

    public function canJoinTeam()
    {
        return 0; //TEMP VALUE
        global $jsDatabase;
        $tr = false;
        $user_id = classJsportUser::getUserId();
        $query = 'Select * FROM '.DB_TBL_PLAYERS.' WHERE usr_id='.intval($user_id);

        $usr = $jsDatabase->selectObject($query);

        if (!JoomsportSettings::get('player_reg') && $usr && $user_id) {
            $tr = true;
        }
        if (JoomsportSettings::get('player_reg')) {
            $tr = true;
        }
        $query = 'SELECT COUNT(*) FROM '.DB_TBL_MODERS.' WHERE tid= '.$this->team_id;
        $is_moder = $jsDatabase->selectValue($query);

        return $tr && $is_moder && JoomsportSettings::get('esport_join_team');
    }
    public function getBoxScore(){
        global $jsDatabase;
            
        $team_id = $this->team_id; 
        $query = "SELECT * FROM ".DB_TBL_BOX_FIELDS
                . " WHERE complex=0 AND published=1 AND displayonfe=1";
        $boxf = $jsDatabase->select($query);
        
        $checkfornull = '';
        for($intA=0;$intA<count($boxf);$intA++){
            if($checkfornull){ $checkfornull .= ' OR ';}
            $checkfornull .= ' boxfield_'.$boxf[$intA]->id.' IS NOT NULL';
        }
        if($checkfornull){
            $query = "SELECT player_id FROM ".DB_TBL_BOX_MATCH
                    ." WHERE team_id = {$team_id}"
                    .($this->season_id?" AND season_id=".$this->season_id:"")
                    . " AND (".$checkfornull.")"
                    ." GROUP BY player_id";
            $players = $jsDatabase->selectColumn($query);
            $html = '';
            if(count($players)){
                $html = $this->getBoxHtml($team_id, $players);
            }
            return $html;
        }
        return null;
    }
    public function getBoxHtml($home_team, $playersNotNull){
        global $wpdb;
        $season_id = $this->season_id;
        $efbox = (int) JoomsportSettings::get('boxExtraField','0');
        
        $html = '';
        $totalSQL = '';
        $bfields = $wpdb->get_results('SHOW COLUMNS FROM '.$wpdb->joomsport_box_match.' LIKE  "boxfield_%"');
        
        for($intA=0;$intA<count($bfields);$intA++){
            $totalSQL .= 'SUM('.$bfields[$intA]->Field .') as '.$bfields[$intA]->Field.',';
        }
        if(!$totalSQL){
            $totalSQL = '*';
        }else{
            $totalSQL .= '1';
        }                
        
        $parentB = array();
        $complexBox = $wpdb->get_results('SELECT * FROM '.$wpdb->joomsport_box.' WHERE parent_id="0" AND published="1"  AND displayonfe="1" ORDER BY ordering,name', 'OBJECT') ;
        for($intA=0;$intA<count($complexBox); $intA++){
            $complexBox[$intA]->extras = array();
            $childBox = array();
            if($complexBox[$intA]->complex == '1'){
                $childBox = $wpdb->get_results(
                    $wpdb->prepare(
                        'SELECT * FROM '.$wpdb->joomsport_box.' WHERE parent_id=%d AND published="1" AND displayonfe="1" ORDER BY ordering,name',
                            array($complexBox[$intA]->id)
                        ),
                    'OBJECT'
                ) ;
                for($intB=0;$intB<count($childBox); $intB++){
                    $options = $childBox[$intB]->options?json_decode($childBox[$intB]->options,true):array();
                    $extras = isset($options['extraVals'])?$options['extraVals']:array();
                    $childBox[$intB]->extras = $extras;
                    if(count($extras)){
                        foreach($extras as $extr){
                            array_push($complexBox[$intA]->extras, $extr);
                        }
                    }
                }
            }else{
                $options = $complexBox[$intA]->options?json_decode($complexBox[$intA]->options,true):array();
                $extras = isset($options['extraVals'])?$options['extraVals']:array();
                $complexBox[$intA]->extras =  $extras;
            }
            $parentB[$intA]['object'] = $complexBox[$intA];
            $parentB[$intA]['childs'] = $childBox;
        }
        
        $th1 = '';
        $th2 = '';
        
        if($efbox){
            $simpleBox = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT id, sel_value as name FROM '.$wpdb->joomsport_ef_select.' WHERE fid=%d ORDER BY eordering,sel_value',
                        array($efbox)
                    ),
                'OBJECT'
            ) ;
            for($intS=0;$intS<count($simpleBox);$intS++){    
                $players = JoomSportHelperObjects::getPlayersByEFonFE($home_team, $season_id, $efbox, $simpleBox[$intS]->id);
                $th1=$th2='';
                $boxtd = array();
                for($intA=0;$intA<count($parentB);$intA++){
                    $box = $parentB[$intA];
                    $intChld = 0;
                    
                    for($intB=0;$intB<count($box['childs']); $intB++){
                        if(!count($box['childs'][$intB]->extras) || in_array($simpleBox[$intS]->id, $box['childs'][$intB]->extras)){
                            $intChld++;
                            $th2 .= "<th>".$box['childs'][$intB]->name."</th>";
                            $boxtd[] =  $box['childs'][$intB]->id;
                            
                        }
                    }

                    if(!count($box['object']->extras) || in_array($simpleBox[$intS]->id, $box['object']->extras)){

                        if($intChld){
                            $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                        }else{
                            $th1 .= '<th rowspan="2">'.$box['object']->name.'</th>';
                            $boxtd[] =  $box['object']->id;
                        }
                    }elseif($intChld){
                        $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                    }
                }
                $html_head = $html_body = '';
                $html_head .= '<div class="table-responsive">
                    <table class="jsBoxStatDIvFE">
                                <thead>
                                    <tr>
                                        <th rowspan="2">'.$simpleBox[$intS]->name.'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                $playersIN = array();
                                    for($intPP=0;$intPP<count($players);$intPP++){
                                        if(in_array($players[$intPP], $playersNotNull)){
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            $player = new classJsportPlayer($players[$intPP],$season_id,false);
                                            $html_body .= $player->getName(true);
                                            $html_body .= '</td>';
                                            $player_stat = $wpdb->get_row("SELECT ".$totalSQL.""
                                                    . " FROM {$wpdb->joomsport_box_match}"
                                                    . " WHERE team_id=".intval($home_team)." AND player_id=".intval($player->object->ID)
                                                    . ($this->season_id?" AND season_id=".intval($this->season_id):""));

                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                                            }
                                            $playersIN[] = $players[$intPP];
                                            $html_body .= '</tr>';
                                        }
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody>';
                    }                
                    
                    if(count($playersIN) && $html_body){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= __('Total','joomsport-sports-league-results-management');
                        $html .= '</td>';
                        $player_stat = $wpdb->get_row("SELECT ".$totalSQL." FROM {$wpdb->joomsport_box_match} WHERE team_id=".intval($home_team)." AND player_id IN (".  implode(',', array_map("absint",$playersIN)).")"
                                . ($this->season_id?" AND season_id=".intval($this->season_id):""));
                        for($intBox=0;$intBox<count($boxtd);$intBox++){
                            
                            $html .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                        }

                        $html .= '</tr>';
                        $html .= '</tfoot>';
                    }
                    if($html_body){
                        $html .=  '</table></div>';
                    }
            }
        }else{
            $th1=$th2='';
            $boxtd = array();
            $players = get_post_meta($home_team,'_joomsport_team_players_'.$season_id,true);
            $players = JoomSportHelperObjects::cleanJSArray($players);
            for($intA=0;$intA<count($parentB);$intA++){
                $box = $parentB[$intA];
                $intChld = 0;
                for($intB=0;$intB<count($box['childs']); $intB++){
                    $intChld++;
                    $th2 .= "<th>".$box['childs'][$intB]->name."</th>";
                    $boxtd[] =  $box['childs'][$intB]->id;
                    
                }

                if($intChld){
                    $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                }else{
                    $th1 .= '<th rowspan="2">'.$box['object']->name.'</th>';
                    $boxtd[] =  $box['object']->id;
                }
                
            }
            $html_head = $html_body = '';
            $html_head .= '<div class="table-responsive"><table class="jsBoxStatDIvFE">
                                <thead>
                                    <tr>
                                        <th rowspan="2">'.__('Player', 'joomsport-sports-league-results-management').'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                    $playersIN = array();
                                    for($intPP=0;$intPP<count($players);$intPP++){
                                        if(in_array($players[$intPP], $playersNotNull)){
                                        
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            $player = new classJsportPlayer($players[$intPP],$season_id,false);
                                            $html_body .= $player->getName(true);
                                            $html_body .= '</td>';
                                            $player_stat = $wpdb->get_row("SELECT ".$totalSQL." FROM {$wpdb->joomsport_box_match} WHERE team_id=".intval($home_team)." AND player_id=".intval($player->object->ID)
                                            . ($this->season_id?" AND season_id=".intval($this->season_id):""));

                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                                            }
                                            $playersIN[] = $players[$intPP];
                                            $html_body .= '</tr>';
                                        }
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody>';
                    }                
                    
                    if(count($playersIN) && $html_body){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= __('Total','joomsport-sports-league-results-management');
                        $html .= '</td>';
                        $player_stat = $wpdb->get_row(
                            $wpdb->prepare(
                                "SELECT ".$totalSQL." FROM {$wpdb->joomsport_box_match} WHERE team_id=%d"
                                . ($this->season_id?" AND season_id=".intval($this->season_id):""),
                                array($home_team))
                        );

                        for($intBox=0;$intBox<count($boxtd);$intBox++){
                            $html .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                        }

                        $html .= '</tr>';
                        $html .= '</tfoot>';
                    }
                    if($html_body){
                        $html .=  '</table></div>';
                    }
        }
        return $html;
        
    }
}

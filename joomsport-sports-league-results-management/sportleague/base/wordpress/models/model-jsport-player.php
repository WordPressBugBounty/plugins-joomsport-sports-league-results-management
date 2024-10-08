<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportPlayer
{
    public $season_id = null;
    public $player_id = null;
    public $lists = null;
    public $row = null;

    public function __construct($id, $season_id = 0)
    {
        $this->season_id = $season_id;
        $this->player_id = $id;

        if (!$this->player_id) {
            die('ERROR! Player ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        $players = jsHelperAllPlayersPosts::getInstance();

        $this->row = $players[intval($this->player_id)];
    }
    public function getRow()
    {
        return $this->row;
    }
    public function loadLists()
    {
        $metadata = get_post_meta($this->player_id,'_joomsport_player_personal',true);
        
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->player_id, '0', $this->season_id);
        if(isset($metadata['home_name']) && ($metadata['home_name'])){
            $tmparr = array(__('Name in home country','joomsport-sports-league-results-management') => $metadata['home_name']);
            $this->lists['ef'] = array_merge($tmparr, $this->lists['ef']);
        }
        if(isset($metadata['short_name']) && ($metadata['short_name'])){
            $tmparr = array(__('Short name','joomsport-sports-league-results-management') => $metadata['short_name']);
            $this->lists['ef'] = array_merge($tmparr, $this->lists['ef']);
        }
        if(isset($metadata['last_name']) && ($metadata['last_name'])){
            $tmparr = array(__('Last Name','joomsport-sports-league-results-management') => $metadata['last_name']);
            $this->lists['ef'] = array_merge($tmparr, $this->lists['ef']);
        }
        if(isset($metadata['first_name']) && ($metadata['first_name'])){
            $tmparr = array(__('First Name','joomsport-sports-league-results-management') => $metadata['first_name']);
            $this->lists['ef'] = array_merge($tmparr, $this->lists['ef']);
            //$this->lists['ef'][__('First Name','joomsport-sports-league-results-management')] = $metadata['first_name'];
        }
        
        $jersey = get_post_meta($this->player_id,'_joomsport_player_jersey_'.$this->season_id,true);
                
        

        $teams = JoomSportHelperObjects::getPlayerTeams($this->season_id, $this->player_id);
        if(count($teams)){
            $tt = '';
            for($intA = 0; $intA < count($teams); $intA++){
                if($intA != 0){
                    $tt .= ', ';
                }
                $tt .= get_the_title($teams[$intA]);
                if($jersey && count($jersey)){
                    foreach ($jersey as $key => $value) {
                        if($teams[$intA] == $key && $value){
                            $tt .= ' (#'.$value.')';
                        }
                    }
                }
            }
            $this->lists['ef'][__('Team','joomsport-sports-league-results-management')] = $tt;
        }
        if (isset($this->row->country_id) && $this->row->country_id) {
            $url = 'components/com_joomsport/img/flags/' . strtolower($this->row->ccode) . '.png';
            if (file_exists($url)) {
                $this->lists['ef'][__('Country','joomsport-sports-league-results-management')] =  '<img src="' . JURI::base() . $url . '" alt="' . $this->row->country . '"/> ' . $this->row->country . ' </span>';
            }
        }
        $this->getPhotos();
        $this->getDefaultImage();
        $this->getHeaderSelect();

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
        $photos = get_post_meta($this->player_id,'vdw_gallery_id',true);

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
        
        $tourns = JoomSportHelperObjects::getPlayerSeasons($this->player_id);

        $javascript = " onchange='fSubmitwTab(this);'";

        $jqre = JoomSportHelperSelectBox::Optgroup('sid', $tourns,$this->season_id, 'class="selectpicker" '.$javascript);

        $this->lists['tourn'] = $jqre;
    }
    public function getBoxScore(){
        global $jsDatabase;
        
        $query = "SELECT * FROM ".DB_TBL_BOX_FIELDS
                . " WHERE complex=0 AND published=1 AND displayonfe=1";
        $boxf = $this->lists["boxf"] = $jsDatabase->select($query);
        
        $checkfornull = '';
        for($intA=0;$intA<count($boxf);$intA++){
            if($checkfornull){ $checkfornull .= ' OR ';}
            $checkfornull .= ' boxfield_'.$boxf[$intA]->id.' IS NOT NULL';
        }
        
        $html = $this->getBoxHtml();
            
        return $html;
        
    }
    
    public function getBoxHtml(){
        global $wpdb, $jsDatabase;
        $season_id = $this->season_id;
        $efbox = (int) JoomsportSettings::get('boxExtraField','0');
        
        $html = '';
        $totalSQL = '';
        $bfields = $this->lists["bfields"] = $wpdb->get_results('SHOW COLUMNS FROM '.$wpdb->joomsport_box_match.' LIKE  "boxfield_%"');
        
        for($intA=0;$intA<count($bfields);$intA++){
            $totalSQL .= 'SUM('.$bfields[$intA]->Field .') as '.$bfields[$intA]->Field.',';
        }
        if(!$totalSQL){
            $totalSQL = '*';
        }else{
            $totalSQL .= '1';
        }                
        $meta = get_post_meta($this->player_id,'_joomsport_player_ef',true);
        
        
        $boxf = $this->lists["boxf"];
        
        $checkfornull = '';
        for($intA=0;$intA<count($boxf);$intA++){
            if($checkfornull){ $checkfornull .= ' OR ';}
            $checkfornull .= ' boxfield_'.intval($boxf[$intA]->id).' IS NOT NULL';
        }
        if(!$checkfornull){
            return '';
        }
        
        $parentB = array();
        $complexBox = $this->lists["complexBox"] = $wpdb->get_results('SELECT * FROM '.$wpdb->joomsport_box.' WHERE parent_id="0" AND published="1"  AND displayonfe="1" ORDER BY ordering,name', 'OBJECT') ;
        for($intA=0;$intA<count($complexBox); $intA++){
            $complexBox[$intA]->extras = array();
            $childBox = array();
            if($complexBox[$intA]->complex == '1'){
                $childBox = $this->lists["boxparentid_".$complexBox[$intA]->id] = $wpdb->get_results(
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
            $efid = 0;
            if(isset($meta[$efbox]) && $meta[$efbox]){
                $efid = $meta[$efbox];
            }
            $simpleBox = $wpdb->get_results(
                $wpdb->prepare(
                'SELECT id, sel_value as name FROM '.$wpdb->joomsport_ef_select.' WHERE fid=%d AND id=%d ORDER BY eordering,sel_value',
                   array($efbox,$efid) ),
                'OBJECT'
            ) ;
            for($intS=0;$intS<count($simpleBox);$intS++){    
                $players = array($this->player_id);
                //$html .= $simpleBox[$intS]->name;
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
                                        <th rowspan="2">'
                                        .($this->season_id?__('Team','joomsport-sports-league-results-management'):__('Season','joomsport-sports-league-results-management'))
                                        .'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                
                                $player_stat = $wpdb->get_results(
                                    "SELECT ".$totalSQL.",team_id,season_id FROM {$wpdb->joomsport_box_match} WHERE player_id=".intval($this->player_id)." AND (".$checkfornull.")"
                                    .($this->season_id?" AND season_id = ".intval($this->season_id)." GROUP BY team_id":" GROUP BY season_id")
                                );
                                    for($intPP=0;$intPP<count($player_stat);$intPP++){
                                            $post_id = $season_id ? $player_stat[$intPP]->team_id: $player_stat[$intPP]->season_id;
                                            
                                            $player = get_post($post_id);    
                                        
                                            
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            
                                            $html_body .= $player->post_title;
                                            $html_body .= '</td>';
                                            
                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat[$intPP])).'</td>';
                                            }
                                            
                                            $html_body .= '</tr>';
                                        
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody>';
                    }  
                    if(count($player_stat) && $html_body){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= __('Total','joomsport-sports-league-results-management');
                        $html .= '</td>';
                        $player_stat = $wpdb->get_row("SELECT ".$totalSQL." FROM {$wpdb->joomsport_box_match} WHERE player_id=".intval($this->player_id).""
                        .($this->season_id?" AND season_id = ".intval($this->season_id):""));
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
            $players = array($this->player_id);
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
                                        <th rowspan="2">'
                                        .($this->season_id?__('Team','joomsport-sports-league-results-management'):__('Season','joomsport-sports-league-results-management'))

                                        .'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                
                                    $player_stat = $wpdb->get_results("SELECT ".$totalSQL.",team_id,season_id FROM {$wpdb->joomsport_box_match} WHERE player_id=".intval($this->player_id)." AND (".$checkfornull.")"
                                .($this->season_id?" AND season_id = ".intval($this->season_id)." GROUP BY team_id":" GROUP BY season_id"));


                                    for($intPP=0;$intPP<count($player_stat);$intPP++){
                                       
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            $post_id = $season_id ? $player_stat[$intPP]->team_id: $player_stat[$intPP]->season_id;
                                            $player = get_post($post_id);
                                            $html_body .= $player->post_title;
                                            $html_body .= '</td>';
                                            
                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat[$intPP])).'</td>';
                                            }
                                            
                                            $html_body .= '</tr>';
                                        
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody>';
                    } 
                    if(count($player_stat) && $html_body){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= __('Total','joomsport-sports-league-results-management');
                        $html .= '</td>';
                        $player_stat = $wpdb->get_row("SELECT ".$totalSQL." FROM {$wpdb->joomsport_box_match} WHERE player_id=".intval($this->player_id)
                        .($this->season_id?" AND season_id = ".intval($this->season_id):""));
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
    
    public function getBoxScoreMatches(){
        global $jsDatabase;
        
        
        $html = $this->getBoxHtmlMatches();
            
        return $html;
        
    }
    
    public function getBoxHtmlMatches(){
        global $wpdb,$jsDatabase;
        $season_id = $this->season_id;
        $efbox = (int) JoomsportSettings::get('boxExtraField','0');
        
        $html = '';
        $totalSQL = '';
        $bfields = $this->lists["bfields"];
        
        for($intA=0;$intA<count($bfields);$intA++){
            $totalSQL .= 'SUM('.$bfields[$intA]->Field .') as '.$bfields[$intA]->Field.',';
        }
        if(!$totalSQL){
            $totalSQL = '*';
        }else{
            $totalSQL .= '1';
        }                
        $meta = get_post_meta($this->player_id,'_joomsport_player_ef',true);

        $boxf = $this->lists["boxf"];
        $checkfornull = '';
        for($intA=0;$intA<count($boxf);$intA++){
            if($checkfornull){ $checkfornull .= ' OR ';}
            $checkfornull .= ' boxfield_'.$boxf[$intA]->id.' IS NOT NULL';
        }
        if(!$checkfornull){
            return '';
        }
        
        $parentB = array();
        $complexBox = $this->lists["complexBox"];
        for($intA=0;$intA<count($complexBox); $intA++){
            $complexBox[$intA]->extras = array();
            $childBox = array();
            if($complexBox[$intA]->complex == '1'){
                $childBox = $this->lists["boxparentid_".$complexBox[$intA]->id];//$wpdb->get_results('SELECT * FROM '.$wpdb->joomsport_box.' WHERE parent_id="'.$complexBox[$intA]->id.'" AND published="1" AND displayonfe="1" ORDER BY ordering,name', 'OBJECT') ;
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
            $efid = 0;
            if(isset($meta[$efbox]) && $meta[$efbox]){
                $efid = $meta[$efbox];
            }
            $simpleBox = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT id, sel_value as name FROM '.$wpdb->joomsport_ef_select.' WHERE fid=%d AND id=%d ORDER BY eordering,sel_value',
                        array($efbox,$efid)
                    ),
                'OBJECT'
            ) ;
            for($intS=0;$intS<count($simpleBox);$intS++){    
                $players = array($this->player_id);
                //$html .= $simpleBox[$intS]->name;
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
                                        <th rowspan="2">'.__('Match','joomsport-sports-league-results-management').'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                
                                $player_stat = $wpdb->get_results("SELECT * FROM {$wpdb->joomsport_box_match} WHERE player_id=".intval($this->player_id)."  AND (".$checkfornull.")"
                                .($this->season_id?" AND season_id = ".intval($this->season_id):""));

                                    for($intPP=0;$intPP<count($player_stat);$intPP++){
                                       
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            $player = get_post($player_stat[$intPP]->match_id);
                                            $html_body .= $player->post_title;
                                            $html_body .= '</td>';
                                            
                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat[$intPP])).'</td>';
                                            }
                                            
                                            $html_body .= '</tr>';
                                        
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody></table></div>';
                    } 
                    
            }
        }else{
            $th1=$th2='';
            $boxtd = array();
            $players = array($this->player_id);
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
                                        <th rowspan="2">'.__('Match', 'joomsport-sports-league-results-management').'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
            
                                    $player_stat = $wpdb->get_results("SELECT * FROM {$wpdb->joomsport_box_match} WHERE player_id=".intval($this->player_id)."  AND (".$checkfornull.")"
                                .($this->season_id?" AND season_id = ".intval($this->season_id):"")
                                    );

                                    for($intPP=0;$intPP<count($player_stat);$intPP++){
                                       
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            $player = get_post($player_stat[$intPP]->match_id);
                                            $html_body .= $player->post_title;
                                            $html_body .= '</td>';
                                            
                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat[$intPP])).'</td>';
                                            }
                                            
                                            $html_body .= '</tr>';
                                        
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody></table></div>';
                    } 
        }
        return $html;
        
    }
    
}

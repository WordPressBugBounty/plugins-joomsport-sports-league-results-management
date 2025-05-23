<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_MODELS.'model-jsport-player.php';
require_once JOOMSPORT_PATH_ENV_CLASSES.'class-jsport-getplayers.php';
require_once JOOMSPORT_PATH_CLASSES.'class-jsport-matches.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-match.php';

class classJsportPlayer
{
    private $id = null;
    public $season_id = null;
    public $object = null;
    public $lists = null;
    public $model = null;
    public $teamID = null;
    public $career = null;
    public $fields = null;

    public function __construct($id = 0, $season_id = null, $loadLists = true)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->id = get_the_ID();
        } else {
            $this->season_id = $season_id;
            $this->id = $id;
        }
        if (!$this->id) {
            die('ERROR! Player ID not DEFINED');
        }
        
        $this->loadObject($loadLists);
    }
    public function __set(string $name, string $value){

    }

    private function loadObject($loadLists)
    {
        $obj = $this->model = new modelJsportPlayer($this->id, $this->season_id);
        $this->object = $obj->getRow();
        if ($loadLists) {
            $this->lists = $obj->loadLists();
            $this->lists['options']['tourn'] = $this->lists['tourn'];
        }else{
            $this->model->getPhotos();
            $this->model->getDefaultImage();
            $this->lists['def_img'] = $this->model->lists['def_img'];
        }
        $this->lists['options']['title'] = $this->getName(false);
    }

    public function getName($linkable = false, $itemid = 0, $display = 0)
    {
        $metadata = get_post_meta($this->id,'_joomsport_player_personal',true);

        $displaySett = JoomsportSettings::get('players_display_name',0);
        if($displaySett == 1 && isset($metadata["short_name"]) && $metadata["short_name"]){
            $pname = $metadata["short_name"];
        }
        
        if(!isset($pname) && isset($metadata['first_name']) && isset($metadata['last_name']) && $displaySett == 2){
            $pname = $metadata['first_name'] .' '.$metadata['last_name'];
        }

        if(!isset($pname) || !trim($pname)){
            $pname = get_the_title($this->id);
        }
        
        
        if($display == 1 && isset($metadata["short_name"]) && $metadata["short_name"]){
            $pname = $metadata["short_name"];
        }
        
        //$pp = get_post($this->id);
        $pp = $this->model->row;

        if(empty($pp)){
            return '';
        }
        if ($pp->post_status != 'publish' || get_post_status($this->id) == 'private') {
            $linkable = false;
        }

        if (JoomsportSettings::get('enbl_playerlinks',1) == '0' && JoomsportSettings::get('enbl_playerlinks_hglteams') == '1'){
            $playersHG = jsHelperHighlightPlayers::getInstance();

            if(!count($playersHG) || !in_array($this->id,$playersHG)){
                $linkable = false;
            }
            // (!in_array($this->id, JoomsportSettings::get('yteams',array())) || JoomsportSettings::get('enbl_playerlinks_hglteams') != '1'))) {
            //$linkable = false;
        }

        if (!$linkable) {
            return $pname;
        }
        $html = '';
        if ($this->id > 0 && $pname) {
            $html = classJsportLink::player($pname, $this->id, $this->season_id,false, $itemid);
        }

        return $html;
    }

    public function getDefaultPhoto()
    {
        return $this->lists['def_img'];
    }
    public function getEmblem($linkable = true, $type = 0, $class = 'emblInline', $width = 0, $light = true, $itemid = 0)
    {
        $html = '';
        //$pp = get_post($this->id);
        $pp = $this->model->row;
        if (empty($pp) || $pp->post_status != 'publish' || get_post_status($this->id) == 'private') {
            $linkable = false;
        }
        if (!isset($this->lists['def_img']) && $type != 10) {
            $this->model->getPhotos();
            $this->model->getDefaultImage();
            $this->lists['def_img'] = $this->model->lists['def_img'];
        }
        if($type == 10){
            $html = jsHelperImages::getEmblemBig($this->lists['def_img'], 10, $class, $width, $light, addslashes($this->getName(false)));
        }else{
            $html = jsHelperImages::getEmblem($this->lists['def_img'], 0, $class, $width, addslashes($this->getName(false)));
        }

        if (JoomsportSettings::get('enbl_playerlogolinks',1) == '0' && JoomsportSettings::get('enbl_playerlinks_hglteams') == '1'){
            $playersHG = jsHelperHighlightPlayers::getInstance();

            if(!count($playersHG) || !in_array($this->id,$playersHG)){
                $linkable = false;
            }
            // (!in_array($this->id, JoomsportSettings::get('yteams',array())) || JoomsportSettings::get('enbl_playerlinks_hglteams') != '1'))) {
            //$linkable = false;
        }
        if ($linkable) {
            $html = classJsportLink::player($html, $this->id, $this->season_id, $itemid, $linkable);
        }

        return $html;
    }

    public function getRow()
    {
        $this->setHeaderOptions();

        return $this;
    }
    public function getRowSimple()
    {
        return $this;
    }

    public function getTabs()
    {
        $tabs = array();
        $intA = 0;
        //main tab
        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = __('Overview','joomsport-sports-league-results-management');
        $tabs[$intA]['body'] = 'object-view.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'js-player';
        //matches
        $this->getMatches();
        if (count($this->lists['matches'])) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_matches';
            $tabs[$intA]['title'] = __('Matches','joomsport-sports-league-results-management');
            $tabs[$intA]['body'] = '';
            $this->lists['pagination'] = $this->lists['match_pagination'];
            $tabs[$intA]['text'] = '<form>'.jsHelper::getMatches($this->lists['matches'], $this->lists, false).'<input type="hidden" name="jscurtab" value="stab_matches" /><input type="hidden" name="sid" value="'.esc_attr($this->season_id).'" /></form>';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'js-match';
        }
        $this->getEvents();
        $this->getMatchPlayed();
        $this->getStatBlock();
        $this->getMatchesBlock();
        $this->getBoxScoreList();
        {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_statistic';
            $tabs[$intA]['title'] = __('Statistic','joomsport-sports-league-results-management');
            $tabs[$intA]['body'] = 'player-stat.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'js-plstat';
        }

        //photos
        if (count($this->lists['photos']) > 1) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_photos';
            $tabs[$intA]['title'] = __('Photos','joomsport-sports-league-results-management');
            $tabs[$intA]['body'] = 'gallery.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'js-photo';
        }
        if ( has_filter( 'joomsport_custom_tab_fe' ) ){
            $tabs = apply_filters("joomsport_custom_tab_fe", $this->id, $tabs);
        }
        

        return $tabs;
    }
    public function getDescription()
    {
        $about = get_post_meta($this->id,'_joomsport_player_about',true);
        return classJsportText::getFormatedText($about);
    }

    public function getEvents()
    {
        $stdoptions = '';
         $stdoptions = "std"; 

        
        $players = classJsportgetplayers::getPlayersFromTeam(array('season_id' => $this->season_id, 'groupby' => 0), $this->id);

        if (isset($players['list'][0])) {
            if($stdoptions == 'std'){
                $this->lists['players'] = $players['list'][0];
            }else{
                $this->lists['players'] = $players['list'];
            }


        }
        //events
        if($this->season_id){
            $this->lists['events_col'] = classJsportgetplayers::getPlayersEvents($this->season_id);
        }else{
            $seasons = JoomSportHelperObjects::getPlayerSeasons($this->id);
            $seasons_arr = array();
            if(count($seasons)){
                foreach($seasons as $seas){
                    
                    for($intA=0;$intA<count($seas);$intA++){
                        
                        $seasons_arr[] = $seas[$intA]->id;
                    }
                }
            }
            if(!count($seasons_arr)){
                $seasons_arr = 0;
            }
            $this->lists['events_col'] = classJsportgetplayers::getPlayersEvents($seasons_arr);
        }
        
    }
    public function getMatchPlayed()
    {
        $this->lists['played_matches'] = null;
        if (JoomsportSettings::get('played_matches')) {
            $this->lists['played_matches'] = classJsportgetplayers::getPlayersPlayedMatches($this->id, 0, $this->season_id);
        }
    }

    public function getMatches()
    {
        $options = array('team_id' => $this->id, 'season_id' => $this->season_id);
        //$link = 'index.php?task=player&id='.$this->id.'&sid='.$this->season_id.'#stab_matches';
        $link = classJsportLink::player('', $this->id, $this->season_id, true);
        $pagination = new classJsportPagination($link);
        $options['limit'] = $pagination->getLimit();
        $options['offset'] = $pagination->getOffset();
        $pagination->setAdditVar('jscurtab', 'stab_matches');
        $obj = new classJsportMatches($options);
        $rows = $obj->getMatchList();
        $pagination->setPages($rows['count']);
        $this->lists['match_pagination'] = $pagination;
        $matches = array();

        if ($rows['list']) {
            foreach ($rows['list'] as $row) {
                $match = new classJsportMatch($row->ID, false);
                $matches[] = $match->getRow();
            }
        }
        $this->lists['matches'] = $matches;
    }
    public function setHeaderOptions()
    {
        if ($this->season_id > 0) {
            $this->lists['options']['calendar'] = $this->season_id;
            $this->lists['options']['standings'] = $this->season_id;
        }

        //social
        if (JoomsportSettings::get('jsbp_player') == '1') {
            $this->lists['options']['social'] = true;
            //classJsportAddtag::addCustom('og:title', $this->getName(false));
            $img = $this->getEmblem();
            if (is_file(JOOMSPORT_PATH_IMAGES.$img)) {
                //classJsportAddtag::addCustom('og:image', JS_LIVE_URL_IMAGES.$img);
            }

            //classJsportAddtag::addCustom('og:description', $this->getDescription());
        }
    }
    public function getYourTeam()
    {
        return '';
    }
    public function getBoxScoreList(){
        $this->lists['boxscore'] = $this->model->getBoxScore();
        $this->lists['boxscore_matches'] = $this->model->getBoxScoreMatches();
    }
    
    public function getStatBlock(){
        global $jsDatabase;
        //$this->getEvents();
        //var_dump($this->lists['players']);
        $jsblock_career = JoomsportSettings::get('jsblock_career');
        $jsblock_career_fields_selected = json_decode(JoomsportSettings::get('jsblock_career_options'),true);
        $jsblock_career_fields_selected = apply_filters("jsblock_career_fields_selected", $jsblock_career_fields_selected, $this->id, $this->season_id);

        if(!$jsblock_career){
            $this->lists['career'] = $this->lists['career_head'] = array();
                return;
            
        }


        $available_options = array(
            'op_mplayed' => array(
                'field' => 'played',
                'text' => __('Matches played','joomsport-sports-league-results-management'),
                'img' => '<img src="'.JOOMSPORT_LIVE_URL_IMAGES_DEF.'matches_played.png" width="24" class="sub-player-ico" title="'.__('Matches played','joomsport-sports-league-results-management').'" alt="'.__('Matches played','joomsport-sports-league-results-management').'" />'
            ),
            'op_mlineup' => array(
                'field' => 'career_lineup',
                'text' => __('Starting lineup','joomsport-sports-league-results-management'),
                'img' => '<img src="'.JOOMSPORT_LIVE_URL_IMAGES_DEF.'squad.png" width="24" class="sub-player-ico" title="'.__('Matches Line Up','joomsport-sports-league-results-management').'" alt="'.__('Matches Line Up','joomsport-sports-league-results-management').'" />'
            ),
            'op_minutes' => array(
                'field' => 'career_minutes',
                'text' => __('Played minutes','joomsport-sports-league-results-management'),
                'img' => '<img src="'.JOOMSPORT_LIVE_URL_IMAGES_DEF.'stopwatch.png" width="24" class="sub-player-ico" title="'.__('Played minutes','joomsport-sports-league-results-management').'" alt="'.__('Played minutes','joomsport-sports-league-results-management').'" />'
            ),
            'op_subsin' => array(
                'field' => 'career_subsin',
                'text' => __('Subs in','joomsport-sports-league-results-management'),
                'img' => '<img src="'.JOOMSPORT_LIVE_URL_IMAGES_DEF.'in-new.png" width="24" class="sub-player-ico" title="'.__('Subs in','joomsport-sports-league-results-management').'" alt="'.__('Subs in','joomsport-sports-league-results-management').'" />'
            ),
            'op_subsout' => array(
                'field' => 'career_subsout',
                'text' => __('Subs out','joomsport-sports-league-results-management'),
                'img' => '<img src="'.JOOMSPORT_LIVE_URL_IMAGES_DEF.'out-new.png" width="24" class="sub-player-ico" title="'.__('Subs out','joomsport-sports-league-results-management').'" alt="'.__('Subs out','joomsport-sports-league-results-management').'" />'
            )
        );
        $resultoptions = array();
        if($jsblock_career_fields_selected && count($jsblock_career_fields_selected)){
            
            foreach($jsblock_career_fields_selected as $block){
                if(isset($available_options[$block])){
                    $resultoptions[] = $available_options[$block];
                }else{
                    $block = str_replace('ev_', 'eventid_', $block);
                    if(isset($this->lists['events_col'][$block])){
                        $resultoptions[] = array(
                            'field' => $block,
                            'text' => $this->lists['events_col'][$block]->getEventName(),
                            'img' => $this->lists['events_col'][$block]->getEmblem(),
                        );
                    }
                }
            }
            
        }

        $output = $outputhead = array();
        if(count($resultoptions)){
            if(!$this->season_id){
                $outputhead[] = __('Season','joomsport-sports-league-results-management');
            }
            $outputhead[] = '';//__('Team','joomsport-sports-league-results-management');
            foreach($resultoptions as $ro){
                if (isset($ro['img']) && $ro['img']) {
                    $outputhead[] = $ro['img'];
                }else
                if (isset($ro['text'])) {
                    $outputhead[] = $ro['text'];
                }
            }
            $intZ = 0;
            if(isset($this->lists['players']) && is_array($this->lists['players'])){
                for($intA=0;$intA<count($this->lists['players']);$intA++){
                    $pl = $this->lists['players'][$intA];
                    if(!$this->season_id){
                        $oseas = new classJsportSeason($pl->season_id);
                        if(!$oseas->object){
                            continue;
                        }
                        $output[$intZ][] = $oseas->modelObj->getName();
                    }
                    if($pl->team_id){

                        $teamObj = new classJsportTeam($pl->team_id,$pl->season_id,false);
                        $output[$intZ][] = $teamObj->getEmblem().$teamObj->getName(true);
                    }else{
                        $output[$intZ][] = '';
                    }  
                    foreach($resultoptions as $ro){
                        if (isset($pl->{$ro['field']})) {
                            if (is_float(floatval($pl->{$ro['field']}))) {
                                $output[$intZ][] = round($pl->{$ro['field']}, 3);
                            } else {
                                $output[$intZ][] = floatval($pl->{$ro['field']});
                            }
                        }
                    }
                    $intZ++;

                }
            }
        }
        $this->lists['career_head'] = $outputhead;
        $this->lists['career'] = $output;

        
    }
    public function getMatchesBlock(){
        global $wpdb, $jsDatabase;

        $kick_events = JoomsportSettings::get('kick_events',array());
        if($kick_events){
            $kick_events = json_decode($kick_events,true);
        }

        if(!JoomsportSettings::get('jsblock_matchstat')){
            $this->lists['career_matches'] = array();
            return;
        }
        
        $argsSeasons = array(
                'posts_per_page'   => -1,
                'offset'           => 0,
                'post_type'        => 'joomsport_season',
                'post_status'      => 'publish'
        );
        $aSeasons = get_posts( $argsSeasons );
        $seasonsArray = array();
        foreach($aSeasons as $aSeason){
            $seasonsArray[] = $aSeason->ID;
        }
        
        $duration = JoomsportSettings::get('jsmatch_duration','');
        $query = 'SELECT s.*,p.postID as post_id'
            .' FROM '.$wpdb->joomsport_squad.' as s'
            .' JOIN '.$wpdb->joomsport_matches.' as p ON p.postID=s.match_id '
            . ' AND p.status="1"'
            .' WHERE 1=1'
            . ($this->season_id?' AND s.season_id='.$this->season_id:(count($seasonsArray)?' AND s.season_id IN ('.implode(',', array_map("absint",$seasonsArray)).')':' AND s.season_id=-1'))
            .' AND s.squad_type != 0 '    
            .' AND s.player_id=%d'
            .' GROUP BY s.match_id'
            .' ORDER BY p.date desc, p.time desc, p.postID desc';
        $matches = $wpdb->get_results($wpdb->prepare($query,array($this->id)));

        

        $html = '';
       for($intA = 0; $intA < count($matches); $intA ++){
           $played_minutes = 0;
           $match = new classJsportMatch($matches[$intA]->post_id, false);
           $partic_home = $match->getParticipantHome();
           $partic_away = $match->getParticipantAway();
           $m_date = get_post_meta($match->id,'_joomsport_match_date',true);
           $m_time = get_post_meta($match->id,'_joomsport_match_time',true);

           $match_date = classJsportDate::getDate($m_date, $m_time);
           $match_duration = $duration;
           $metadata = get_post_meta($match->id,'_joomsport_match_general',true);
            if(isset($metadata['match_duration']) && $metadata['match_duration'] != ''){
                $match_duration = $metadata['match_duration'];
            }

            $matchesSub = $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT s.*'
                    .' FROM '.$wpdb->joomsport_squad.' as s'
                    .' WHERE s.squad_type != 0 '
                    .' AND s.player_id=%d'
                    .' AND s.match_id=%d',
                    array($this->id, $matches[$intA]->post_id)
                )
            );
            if($match_duration){
                for($intZ=0;$intZ<count($matchesSub);$intZ++){
                    if($matchesSub[$intZ]->squad_type == 1){
                        $min = 0;
                        if($matchesSub[$intZ]->minutes){
                            $min = $matchesSub[$intZ]->minutes;
                        }
                        if(!$min){
                            $min = $match_duration;

                            if(is_array($kick_events) && count($kick_events)){
                                $query = "SELECT minutes"
                                    . " FROM ".DB_TBL_MATCH_EVENTS
                                    . " WHERE match_id = ".(intval($matches[$intA]->post_id))
                                    . " AND player_id = ".intval($this->id)
                                    . " AND e_id IN (".implode(',', $kick_events).")"
                                    . " ORDER BY minutes asc"
                                    . " LIMIT 1";
                                $kickOut = (int) $jsDatabase->selectValue($query);

                                if($kickOut){
                                    $min = $kickOut;
                                }
                            }
                        }
                        $played_minutes += $min;
                    }else{
                        $min = 0;
                        if($matchesSub[$intZ]->minutes){
                            $min = $matchesSub[$intZ]->minutes;
                        }

                        if($min){

                            if($matchesSub[$intZ]->is_subs == -1){
                                $kickOut = 0;
                                if(is_array($kick_events) && count($kick_events)){
                                    $query = "SELECT minutes"
                                        . " FROM ".DB_TBL_MATCH_EVENTS
                                        . " WHERE match_id = ".(intval($matches[$intA]->post_id))
                                        . " AND player_id = ".intval($this->id)
                                        . " AND e_id IN (".implode(',', $kick_events).")"
                                        . " ORDER BY minutes asc"
                                        . " LIMIT 1";
                                    $kickOut = (int) $jsDatabase->selectValue($query);

                                }
                                if($kickOut){
                                    $played_minutes += $kickOut - $min;

                                }else{
                                    $played_minutes += $match_duration - $min;
                                }

                            }else if($matchesSub[$intZ]->is_subs == 1){

                                $played_minutes += $min - $match_duration;
                            }

                        }
                    } 
                }
            }else{
                $played_minutes = '';
            }
           
           $match_events = '';

            $ev = $jsDatabase->select(
                $wpdb->prepare(
                    'SELECT * FROM ('
                    .' SELECT e_id,minutes, eordering'
                    .' FROM '.$wpdb->joomsport_match_events
                    .' WHERE match_id = %d'
                    .' AND player_id=%d'
                    .' UNION ALL'
                    .' SELECT mad.e_id,me.minutes, me.eordering'
                    .' FROM '.$wpdb->joomsport_match_events_addit.' as mad'
                    .' JOIN '.$wpdb->joomsport_match_events.' as me ON me.id=mad.parent_event'
                    .' WHERE me.match_id = %d'
                    .' AND mad.player_id=%d'
                    .') as a '
                    .' ORDER BY eordering,minutes'
                    ,array($matches[$intA]->post_id,$this->id,$matches[$intA]->post_id,$this->id)
                )

            );
            
            for($intG=0;$intG<count($ev);$intG++){
                if($ev[$intG]->e_id) {
                    $evObj = new classJsportEvent($ev[$intG]->e_id);
                    $title = $evObj->getEventName();
                    if ($ev[$intG]->minutes) {
                        $title .= ' ' . $ev[$intG]->minutes . '\'';
                    }
                    $match_events .= $evObj->getEmblem(false, $title);
                }
            }
           if(count($matchesSub) || $match_events){
               
           $html .= '<div class="jstable-row">
                            <div class="jstable-cell jsMatchDivTime">
                                <div class="jsDivLineEmbl">'

                                    .$match_date
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivHome">
                                <div class="jsDivLineEmbl">'

                                    .  (isset($partic_home)?jsHelper::nameHTML($partic_home->getName(true)):"")
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivHomeEmbl">'
                                .'<div class="jsDivLineEmbl pull-right">'
                                    .(isset($partic_home)?$partic_home->getEmblem():"")

                                .'</div>

                            </div>
                            <div class="jstable-cell jsMatchDivScore">
                                '.jsHelper::getScore($match).'
                            </div>
                            <div class="jstable-cell jsMatchDivAwayEmbl">
                                <div class="jsDivLineEmbl">'

                                        .(isset($partic_away)?$partic_away->getEmblem():"")
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivAway">'
                                .'<div class="jsDivLineEmbl">'

                                        .(isset($partic_away)?jsHelper::nameHTML($partic_away->getName(true), 0):"")

                                .'</div>    
                            </div>'
                            .'<div class="jstable-cell">'
                                .$match_events
                            .'</div>'
                            .'<div class="jstable-cell">'
                                .($played_minutes?$played_minutes.'\'':'')
                            .'</div>
                        </div>    ';
           }
       }
       $this->lists['career_matches'] = $html;
        
    }  
}

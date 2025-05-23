<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class JoomsportPageSettings{
    public static function action(){
        global $wpdb;

        //add meta for moderators
        if(isset($_REQUEST["pullModerators"]) && $_REQUEST["pullModerators"] == 1){
            JoomSportUserRights::setModeratorMeta();
        }


        if (isset($_REQUEST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), basename(__FILE__))) {
            //general settings

            $general = array_map( 'sanitize_text_field', isset($_POST['general'])?wp_unslash( $_POST['general'] ):array() );
            if(isset($_POST['mdf']) && count($_POST['mdf'])){
                $mdf = array_map( 'sanitize_text_field', wp_unslash( $_POST['mdf'] ) );
                $general = array_merge($general,$mdf);
            }
            if(isset($_POST['yteams']) && count($_POST['yteams'])){

                $yteams = array_map( 'sanitize_text_field', wp_unslash( $_POST['yteams'] ) );
                $yteams['yteams'] = $yteams;
                $general = array_merge($general,$yteams);
            }
            $mstatuses = array();
            if (isset($_POST['mstatusesId']) && count($_POST['mstatusesId'])) {
                for ($intA = 0; $intA < count($_POST['mstatusesId']); ++$intA) {

                    $mstatusesId = isset($_POST['mstatusesId'][$intA])?intval($_POST['mstatusesId'][$intA]):0;
                    $mstatusesName = isset($_POST['mstatusesName'][$intA])?sanitize_text_field(wp_unslash($_POST['mstatusesName'][$intA])):0;
                    $mstatusesShortName = isset($_POST['mstatusesShortName'][$intA])?sanitize_text_field(wp_unslash($_POST['mstatusesShortName'][$intA])):0;

                    if ($mstatusesId == 0 && $mstatusesName && $mstatusesShortName) {
                        $wpdb->insert($wpdb->joomsport_match_statuses,array('stName' => $mstatusesName,'stShort' => $mstatusesShortName,'ordering' => $intA),array('%s','%s','%d'));
                        $id = $wpdb->insert_id;
                    } elseif ($mstatusesId) {
                        $wpdb->update($wpdb->joomsport_match_statuses,array('stName' => $mstatusesName,'stShort' => $mstatusesShortName,'ordering' => $intA),array("id" => $mstatusesId),array('%s','%s','%d'),array('%d'));
                        $id = intval($mstatusesId);
                    }
                    $mstatuses[] = $id;
                }
            }
            if (count($mstatuses)) {
                $wpdb->query('DELETE FROM '.$wpdb->joomsport_match_statuses.' WHERE id NOT IN ('.implode(',', array_map('absint',$mstatuses)).')');
            }
            
            $options = wp_json_encode($general);
            
            $wpdb->update($wpdb->joomsport_config, array('cValue' => $options), array('cName' => 'general'), array('%s'), array('%s'));
            

            //layouts settings
            $layouts = isset($_POST['layouts'])?array_map( 'sanitize_text_field', wp_unslash( $_POST['layouts'] ) ):null;
            if(isset($_POST['layouts']['columnshort'])) {
                $layouts_columnshort = array_map('sanitize_text_field', wp_unslash($_POST['layouts']['columnshort']));
                if ($layouts_columnshort) {
                    $layouts['columnshort'] = wp_json_encode($layouts_columnshort);
                }
            }
            if(isset($_POST['layouts']['jsblock_career_options'])) {
                $layouts_jsblock_career_options = array_map('sanitize_text_field', wp_unslash($_POST['layouts']['jsblock_career_options']));

                if (isset($layouts['jsblock_career_options']) && $layouts_jsblock_career_options) {
                    $layouts['jsblock_career_options'] = wp_json_encode($layouts_jsblock_career_options);
                }
            }
            if(isset($_POST['layouts']['opposite_events'])) {
                $layouts_opposite_events = array_map('sanitize_text_field', wp_unslash($_POST['layouts']['opposite_events']));

                if (isset($layouts_opposite_events) && $layouts_opposite_events) {
                    $layouts['opposite_events'] = wp_json_encode($layouts_opposite_events);
                }
            }
            if(isset($_POST['layouts']['kick_events'])) {
                $layouts_kick_events = array_map('sanitize_text_field', wp_unslash($_POST['layouts']['kick_events']));

                if (isset($layouts_kick_events) && $layouts_kick_events) {
                    $layouts['kick_events'] = wp_json_encode($layouts_kick_events);
                }
            }
            if(isset($_POST['layouts']['avgevents_events'])) {
                $layouts_avgevents_events = array_map('sanitize_text_field', wp_unslash($_POST['layouts']['avgevents_events']));

                if (isset($layouts_avgevents_events) && $layouts_avgevents_events) {
                    $layouts['avgevents_events'] = wp_json_encode($layouts_avgevents_events);
                }
            }

            $options = wp_json_encode($layouts);
            $wpdb->update($wpdb->joomsport_config, array('cValue' => $options), array('cName' => 'layouts'), array('%s'), array('%s'));
            
            //other settings
            if(isset($_POST['other_settings']) && count($_POST['other_settings'])){

                $other_settings = array_map( 'sanitize_text_field', wp_unslash( $_POST['other_settings'] ) );
                $options = wp_json_encode($other_settings);
                $wpdb->update($wpdb->joomsport_config, array('cValue' => $options), array('cName' => 'other'), array('%s'), array('%s'));
            }



            
        }
        JoomsportSettings::getInstance();
        
        $lists = array();
        $is_field_yn = array();
        $is_field_yn[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field_yn[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        
        $is_field_tourntype = array();
        $is_field_tourntype[] = JoomSportHelperSelectBox::addOption(0, __("Team", "joomsport-sports-league-results-management"));
        $is_field_tourntype[] = JoomSportHelperSelectBox::addOption(1, __("Single", "joomsport-sports-league-results-management"));
        
        $is_field_date = array();
        $is_field_date[] = JoomSportHelperSelectBox::addOption("d-m-Y H:M", "d-m-Y H:M");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("d.m.Y H:M", "d.m.Y H:M");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("Y.m.d H:M", "Y.m.d H:M");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("m-d-Y I:M p", "m-d-Y I:M p");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("m B, Y H:M", "m B, Y H:M");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("m B, Y I:H p", "m B, Y I:H p");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("m b, Y I:H p", "m b, Y I:H p");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("d-m-Y", "d-m-Y");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("A d B, Y H:M","A d B, Y H:M");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("d/m/Y H:M", "d/m/Y H:M");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("j M H:M", "j M H:M");

        $limit_array = array();
        $limit_array[] = JoomSportHelperSelectBox::addOption(5,   "5");
        $limit_array[] = JoomSportHelperSelectBox::addOption(10,  "10");
        $limit_array[] = JoomSportHelperSelectBox::addOption(20,  "20");
        $limit_array[] = JoomSportHelperSelectBox::addOption(25,  "25");
        $limit_array[] = JoomSportHelperSelectBox::addOption(50,  "50");
        $limit_array[] = JoomSportHelperSelectBox::addOption(100, "100");
        $limit_array[] = JoomSportHelperSelectBox::addOption(0,   __("All", "joomsport-sports-league-results-management"));

        $lists['mday_extra'] = $wpdb->get_results("SELECT ef.*
		            FROM ".$wpdb->joomsport_ef." as ef		            
		            WHERE ef.published=1 AND ef.type='2'
		            ORDER BY ef.ordering");
        
        
        $args = array(
                'offset'           => 0,
                'orderby'          => 'title',
                'order'            => 'ASC',
                'post_type'        => 'joomsport_team',
                'post_status'      => 'publish',
                'posts_per_page'   => -1,
                'update_post_meta_cache' => false,
        );
        $teamlist = get_posts( $args );
        

        $lists['adf_player'] = $wpdb->get_results("SELECT * FROM ".$wpdb->joomsport_ef
            . " WHERE type='0' AND season_related='0' AND published='1'"
            . " ORDER BY ordering");

        $plsquadf = $wpdb->get_results("SELECT id,name FROM ".$wpdb->joomsport_ef
            . " WHERE type='0' AND field_type IN('0','3') AND published='1'"
            . " ORDER BY ordering");
        
        if(JoomsportSettings::get('enbl_player_system_num',0) == '1'){
            $std = new stdClass();
            $std->id = -1;
            $std->name = __("System player number", "joomsport-sports-league-results-management");
            if($plsquadf){
                $plsquadf = array_merge($plsquadf, array($std));
            }else{
                $plsquadf = array($std);
            }
        }
        $lists['adf_player_squad'] = $plsquadf;

        $lists['adf_team'] = $wpdb->get_results("SELECT * FROM ".$wpdb->joomsport_ef
            . " WHERE type='1' AND season_related='0' "
            . " ORDER BY ordering");
        
        $is_field_inv = array();
        $is_field_inv[] = JoomSportHelperSelectBox::addOption(0, __("Moderator adds player to team", "joomsport-sports-league-results-management"));
        $is_field_inv[] = JoomSportHelperSelectBox::addOption(1, __("Moderator invites player to team", "joomsport-sports-league-results-management"));
        

        $adf = $wpdb->get_results("SELECT name, CONCAT(id,'_1') as id"
            . " FROM ".$wpdb->joomsport_ef.""
            . " WHERE type='0' AND (field_type = 0 OR field_type = 3)"
            . " ORDER BY name");
        $alltmp['op'] = JoomSportHelperSelectBox::addOption(0, __('Name','joomsport-sports-league-results-management'));

        if(count($adf)){
            $alltmp[__('Extra fields','joomsport-sports-league-results-management')] = $adf;
        }

        $events_cd = $wpdb->get_results("SELECT CONCAT(ev.id,'_2') as id,ev.e_name as name
		            FROM ".$wpdb->joomsport_events." as ev
                            WHERE ev.player_event IN (1, 2)
		            ORDER BY ev.e_name");
        if(count($events_cd)){
            $alltmp[__('Events','joomsport-sports-league-results-management')] = $events_cd;
        }
        
        $is_field_pltab = array();
        $is_field_pltab[] = JoomSportHelperSelectBox::addOption(0, __("Player statistics list", "joomsport-sports-league-results-management"));
        $is_field_pltab[] = JoomSportHelperSelectBox::addOption(1, __("Player photos", "joomsport-sports-league-results-management"));
        

        $adf_se = $wpdb->get_results("SELECT name, CONCAT(id,'_1') as id"
            . " FROM ".$wpdb->joomsport_ef."
		            WHERE type='0' AND field_type = 3
		            ORDER BY ordering");

        $alltmp_se['op'] = JoomSportHelperSelectBox::addOption(0, __('Name','joomsport-sports-league-results-management'));

        if(count($adf_se)){
            $alltmp_se[__('Extra fields','joomsport-sports-league-results-management')] = $adf_se;
        }
        
        $lists['mstatuses'] = $wpdb->get_results('SELECT * FROM '.$wpdb->joomsport_match_statuses.' ORDER BY ordering');

        $adfSel = $wpdb->get_results("SELECT name, id"
            . " FROM ".$wpdb->joomsport_ef.""
            . " WHERE type='0' AND field_type = 3"
            . " ORDER BY name");

        $adfText = $wpdb->get_results("SELECT name, id"
            . " FROM ".$wpdb->joomsport_ef.""
            . " WHERE type='0' AND field_type = 0"
            . " ORDER BY name");

        $adfPlayer = $wpdb->get_results("SELECT name, id"
            . " FROM ".$wpdb->joomsport_ef.""
            . " WHERE type='0'"
            . " ORDER BY name");



        $lists['available_options'] = JoomsportSettings::getStandingColumns();

        $events = $wpdb->get_results("SELECT CONCAT('ev_',id) as id,e_name as name FROM {$wpdb->joomsport_events} WHERE player_event != 0");
        $eventsOnly = $wpdb->get_results("SELECT id,e_name as name FROM {$wpdb->joomsport_events} WHERE player_event != 0");
        
        $is_data_career = array();

        $is_data_career[] = JoomSportHelperSelectBox::addOption('op_mplayed', __('Matches played','joomsport-sports-league-results-management'));
        $is_data_career[] = JoomSportHelperSelectBox::addOption('op_mlineup', __('Matches Line Up','joomsport-sports-league-results-management'));
        $is_data_career[] = JoomSportHelperSelectBox::addOption('op_minutes', __('Played minutes','joomsport-sports-league-results-management'));
        $is_data_career[] = JoomSportHelperSelectBox::addOption('op_subsin', __('Subs in','joomsport-sports-league-results-management'));
        $is_data_career[] = JoomSportHelperSelectBox::addOption('op_subsout', __('Subs out','joomsport-sports-league-results-management'));
        if(!empty($events)){
           $is_data_career = array_merge($is_data_career, $events);
        }

        $adfTeam = $wpdb->get_results("SELECT name, id"
            . " FROM ".$wpdb->joomsport_ef."
		            WHERE type='1' AND season_related = '0'
		            ORDER BY ordering");
        
        $is_data_shortened = array();

        $is_data_shortened[] = JoomSportHelperSelectBox::addOption('-1', __('System team short name','joomsport-sports-league-results-management'));
        if(count($adfTeam)){
            $is_data_shortened = array_merge($is_data_shortened, $adfTeam);
        }

        $Allevents = $wpdb->get_results("SELECT id,e_name as name FROM {$wpdb->joomsport_events} ORDER BY ordering,e_name");



        wp_enqueue_script( 'joomsport-colorgrid-js', plugins_url('../../includes/3d/color_piker/201a.js', __FILE__) );
        require_once JOOMSPORT_PATH_HELPERS . 'tabs.php';
        $etabs = new esTabs();
        ?>
        <script type="text/javascript">
		
                
            function addMatchStatus(){
                if(jQuery("#custstat_name").val() && jQuery("#custstat_shortname").val()){
                    var tr = jQuery("<tr>");
                    tr.append('<td><input type="hidden" name="mstatusesId[]" value="0" /><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo esc_attr(__('Delete', 'joomsport-sports-league-results-management'));?>"><i class="fa fa-trash" aria-hidden="true"></i></a></td>');
                    tr.append('<td><input type="text" name="mstatusesName[]" value="'+jQuery("#custstat_name").val()+'" /></td>');
                    tr.append('<td><input type="text" name="mstatusesShortName[]" value="'+jQuery("#custstat_shortname").val()+'" /></td>');
                    jQuery('#matchStatusesTable').append(tr);
                    jQuery("#custstat_name").val("");
                    jQuery("#custstat_shortname").val("");
                }
            }
            function Delete_tbl_row(element) {
                    var del_index = element.parentNode.parentNode.sectionRowIndex;
                    var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
                    element.parentNode.parentNode.parentNode.deleteRow(del_index);
            }

        </script>
        <div class="jsSettingsPage">
            <div class="jsBEsettings" style="padding:0px;">
                <!-- <tab box> -->
                <ul class="tab-box">
                    <?php
                    echo wp_kses_post($etabs->newTab(__('General','joomsport-sports-league-results-management'), 'main_conf', '', 'vis'));
                    
                    echo wp_kses_post($etabs->newTab(__('Moderator','joomsport-sports-league-results-management'), 'moder_conf', ''));
                    
                    //echo $etabs->newTab("Team moderation", 'team_conf', '');
                    //echo $etabs->newTab("Season administration", 'season_conf', '');
                    echo wp_kses_post($etabs->newTab(__('Layouts','joomsport-sports-league-results-management'), 'layout_conf', ''));
                    ?>
                </ul>	
                <div style="clear:both"></div>
            </div>
            
        <div class="mgl-panel-wrap">
            <script type="text/javascript" id="UR_initiator"> (function () { var iid = 'uriid_'+(new Date().getTime())+'_'+Math.floor((Math.random()*100)+1); if (!document._fpu_) document.getElementById('UR_initiator').setAttribute('id', iid); var bsa = document.createElement('script'); bsa.type = 'text/javascript'; bsa.async = true; bsa.src = '//beardev.useresponse.com/sdk/supportCenter.js?initid='+iid+'&wid=6'; (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(bsa); })(); </script>
            <form method="post">
            <div  id="main_conf_div" class="tabdiv">
                <div class="jsrespdiv6">
                <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo esc_html__('General', 'joomsport-sports-league-results-management');?>
                    </div>
                    <div class="jsBEsettings">
                        <table class="adminlistsNoBorder">
                            
                            <tr>
                                        <td>
                                            <?php echo esc_html__('League type', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('general[tournament_type]', $is_field_tourntype,JoomsportSettings::get('tournament_type',0),'',array('lclasses'=>array(1,1))), JoomsportSettings::getKsesRadio());
                                            ?>

                                        </td>
                                </tr>
                                <tr>
                                        <td width="270">
                                            <?php echo esc_html__('Date format', 'joomsport-sports-league-results-management');?>

                                        </td>
                                        <td>
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Simple('general[dateFormat]', $is_field_date,JoomsportSettings::get('dateFormat','d-m-Y H:M'),'',false), JoomsportSettings::getKsesSelect());
        
                                            ?>


                                        </td>

                                </tr>

                                
                                <?php
                                $stdoptions = '';
                                 $stdoptions = "std"; 
                                ?>

                                <tr>
                                        <td>
                                            <?php echo esc_html__('Enable Club', 'joomsport-sports-league-results-management');?>

                                        </td>
                                        <td>
                                            <?php 
                                            
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('general[enbl_club]', $is_field_yn,JoomsportSettings::get('enbl_club',0)), JoomsportSettings::getKsesRadio());
                                            
                                            ?>

                                        </td>
                                </tr>



                                <tr>
                                        <td>
                                            <?php echo esc_html__('Enable Venue', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('general[unbl_venue]', $is_field_yn,JoomsportSettings::get('unbl_venue',1)), JoomsportSettings::getKsesRadio());
                                            
                                            ?>

                                        </td>

                                </tr>
                                <tr>
                                    <td>
                                        <?php echo esc_html__('Enable Venue Link', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php

                                        echo wp_kses(JoomSportHelperSelectBox::Radio('general[unbl_venue_link]', $is_field_yn,JoomsportSettings::get('unbl_venue_link',1)), JoomsportSettings::getKsesRadio());

                                        ?>

                                    </td>

                                </tr>
                                <tr>
                                        <td>
                                            <?php echo esc_html__('Enable JoomSport branding', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('general[jsbrand_on]', $is_field_yn,JoomsportSettings::get('jsbrand_on',1),''), JoomsportSettings::getKsesRadio());
                                            ?>


                                        </td>
                                </tr>
                                <tr>
                                        <td width="270">
                                            <?php echo esc_html__('Group Box Score by', 'joomsport-sports-league-results-management');?>

                                        </td>
                                        <td>
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Simple('general[boxExtraField]', $adfSel,JoomsportSettings::get('boxExtraField','0'),'',true), JoomsportSettings::getKsesSelect());
        
                                            ?>


                                        </td>

                                </tr>
                                <tr>
                                        <td>
                                            <?php echo esc_html__('Hierarchical seasons', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('general[hierarchical_season]', $is_field_yn,JoomsportSettings::get('hierarchical_season', 0),''), JoomsportSettings::getKsesRadio());
                                            ?>

                                        </td>
                                </tr>
                            <tr>
                                <td width="270">
                                    <?php echo esc_html__('Default pagination', 'joomsport-sports-league-results-management');?>
                                </td>
                                <td><?php
                                    // classJsportPagination
                                    echo wp_kses(JoomSportHelperSelectBox::Simple('general[jsportPagination]', $limit_array,JoomsportSettings::get('jsportPagination','25'),'',false), JoomsportSettings::getKsesSelect()); ?>
                                </td>
                            </tr>
                            <tr>
                            <tr>
                                <td>
                                    <?php echo esc_html__('Match result, score separator', 'joomsport-sports-league-results-management');?>
                                </td>
                                <td>
                                    <input type="text" maxlength="3" name="general[jsconf_score_separator]" style="width:40px;" value="<?php echo esc_attr(JoomsportSettings::get('jsconf_score_separator', '-'));?>" ;" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo esc_html__('Match fixtures, score separator', 'joomsport-sports-league-results-management');?>
                                </td>
                                <td>
                                    <input type="text" maxlength="3" name="general[jsconf_score_separator_vs]" style="width:40px;" value="<?php echo esc_attr(JoomsportSettings::get('jsconf_score_separator_vs', 'v'));?>" ;" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo esc_html__('Match header and url: divider', 'joomsport-sports-league-results-management');?>
                                </td>
                                <td>
                                    <input type="text" maxlength="3" name="general[jsconf_home_away_separator_vs]" style="width:40px;" value="<?php echo esc_attr(JoomsportSettings::get('jsconf_home_away_separator_vs', 'vs'));?>" ;" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo esc_html__('Google Maps API Key', 'joomsport-sports-league-results-management');?>
                                </td>
                                <td>
                                    <input type="text" maxlength="100" name="general[gmap_api_key]" style="width:350px;"  value="<?php echo esc_attr(JoomsportSettings::get('gmap_api_key', 'AIzaSyA1NR_RmgpTgzBwKwrvt_yGXw5Cw4Kj_io'));?>" ;" />
                                </td>
                            </tr>
                                
                        </table>
                    </div>
                </div>
                <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('Quick matchday creation', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            <table class="">
                                <tr>
                                    <th align="left">
                                        <?php echo esc_html__('Field', 'joomsport-sports-league-results-management');?>
                                    </th>
                                    <th>
                                        <?php echo esc_html__('Show on page', 'joomsport-sports-league-results-management');?>
                                    </th>
                                </tr>
                                <tr>
                                    <td width="280">
                                        <?php echo esc_html__('Extra Time', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('mdf[mdf_et]', $is_field_yn,JoomsportSettings::get('mdf_et'),''), JoomsportSettings::getKsesRadio());
                                        ?>

                                    </td>

                                </tr>
                                <tr>
                                        <td width="200">
                                            <?php echo esc_html__('Status', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('mdf[mdf_played]', $is_field_yn,JoomsportSettings::get('mdf_played',1),''), JoomsportSettings::getKsesRadio());
                                            ?>
                                        </td>

                                </tr>
                                <tr>
                                        <td width="200">
                                            <?php echo esc_html__('Date', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('mdf[mdf_date]', $is_field_yn,JoomsportSettings::get('mdf_date',1),''), JoomsportSettings::getKsesRadio());
                                            ?>
                                        </td>

                                </tr>
                                <tr>
                                        <td width="200">
                                            <?php echo esc_html__('Time', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('mdf[mdf_time]', $is_field_yn,JoomsportSettings::get('mdf_time',1),''), JoomsportSettings::getKsesRadio());
                                            ?>
                                        </td>

                                </tr>
                                <?php
                                
                                ?>
                                <tr>
                                        <td width="200">
                                            <?php echo esc_html__('Venue', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('mdf[mdf_venue]', $is_field_yn,JoomsportSettings::get('mdf_venue'),''), JoomsportSettings::getKsesRadio());
                                            ?>
                                        </td>

                                </tr>
                                <?php
                                
                                ?>
                                <?php
                                if(isset($lists['mday_extra']) && count($lists['mday_extra'])){
                                    foreach ($lists['mday_extra'] as $extra) {
                                        $extraname = 'extra_'.$extra->id;
                                        ?>
                                        <tr>
                                            <td width="200">
                                                    <?php echo esc_html($extra->name); ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo wp_kses(JoomSportHelperSelectBox::Radio('mdf[extra_'.$extra->id.']', $is_field_yn,JoomsportSettings::get('extra_'.$extra->id),''), JoomsportSettings::getKsesRadio());
                                                ?>
                                                
                                            </td>

                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </table>    
                        </div>    
                    </div>
                <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo '<a href="http://app.joomsport.com/?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro" target="_blank">Mobile Application</a> settings';?>
                    </div>
                    <div class="jsBEsettings">
                        <?php ?>
                        <?php  echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>';  ?>
                    </div>
                </div>   
                <?php if(!is_plugin_active( 'joomsport-api/joomsport-api.php' )){?>    
                <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo 'Soccer Data API subscription';?>
                    </div>
                    <div class="jsBEsettings">

                        Data API allows you to import and update popular soccer leagues. 800+ leagues available. <a href="https://beardev.com/contact-us?utm_source=sittings&utm_medium=web&utm_campaign=passive" target="_blank">Contact us</a> for details.

                    </div>
                </div> 
                <?php } ?>    
            </div>
            <div class="jsrespdiv6 jsrespmarginleft2">
                <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo esc_html__('Team highlighting', 'joomsport-sports-league-results-management');?>
                    </div>
                    <div class="jsBEsettings">
                        <table class="adminlistsNoBorder">
                            <tr>
                                <td width="30%">
                                    <?php echo esc_html__('Highlight selected teams in season standings', 'joomsport-sports-league-results-management');?>
                                </td>
                                <td>
                                    <div class="controls">
                                        <fieldset class="radio btn-group">
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('general[highlight_team]', $is_field_yn,JoomsportSettings::get('highlight_team'),''), JoomsportSettings::getKsesRadio());
                                            ?>
                                        </fieldset>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="jsEnblHGL">
                                    <?php echo esc_html__('Highlight color', 'joomsport-sports-league-results-management');?>
                                </td>
                                <td class="jsEnblHGL">
                                        <div id="colorpicker201" class="colorpicker201"></div>
                                        <input class="button" type="button" style="cursor:pointer;" onclick="showColorGrid2('yteam_color','sample_1');" value="...">&nbsp;<input type="text" name="general[yteam_color]" id="yteam_color" size="5" style="width:70px;margin-bottom: 0px;" maxlength="30" value="<?php echo esc_attr(JoomsportSettings::get('yteam_color',''));?>" /><input type="text" id="sample_1" size="1" value="" style="margin-bottom: 0px;background-color:<?php echo esc_attr(JoomsportSettings::get('yteam_color',''));?>" class="color-kind" />
                                </td>

                            </tr>
                            <tr>
                                <td class="jsEnblHGL">
                                    <?php echo esc_html__('Select teams', 'joomsport-sports-league-results-management');?>
                                </td>
                            
                            
                                <td class="jsEnblHGL">

                                    <?php
                                    if(count($teamlist)){
                                        echo '<select name="yteams[]" class="jswf-chosen-select" data-placeholder="'.esc_attr(__('Add item','joomsport-sports-league-results-management')).'" multiple>';
                                        foreach ($teamlist as $tm) {
                                            $selected = '';
                                            if(in_array($tm->ID, JoomsportSettings::get('yteams',array()))){
                                                $selected = ' selected';
                                            }
                                            echo '<option value="'.esc_attr($tm->ID).'" '.esc_attr($selected).'>'.esc_html(get_the_title($tm->ID)).'</option>';
                                        }
                                        echo '</select>';
                                    }
                                    ?>

                                </td>
                                            
                            </tr>
                        </table>

                    </div>
                </div>
                
                <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo esc_html__('Custom match statuses', 'joomsport-sports-league-results-management');?>
                    </div>
                    <div class="jsBEsettings">
                        <?php ?>
                        <?php  echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>';  ?>
                    </div>
                </div>

                <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo esc_html__('Post titles', 'joomsport-sports-league-results-management');?>
                    </div>
                    <div class="jsBEsettings">
                        <?php ?>
                        <?php  echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>';  ?>
                    </div>
                </div>
            </div>
            <div style="clear:both;" ></div>
        </div>
                
        <div  id="moder_conf_div" class="tabdiv visuallyhidden">
            <div class="jsrespdiv12">
                <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo esc_html__('Permissions', 'joomsport-sports-league-results-management');?>
                    </div>
                    <div class="jsBEsettings">
                        <?php  echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; ?>
                        <?php ?>  
                    </div>

                </div>

                  

            </div>

        </div>
         
            <div id="season_conf_div" class="tabdiv visuallyhidden">
                <div class="jsrespdiv6">
                        <div class="jsBepanel">
                            <div class="jsBEheader">
                                <?php echo esc_html__('Team tournament', 'joomsport-sports-league-results-management');?>
                            </div>
                            <div class="jsBEsettings">

                                <table class="adminlistsNoBorder">

                                        <tr>
                                                <td><?php echo esc_html__('Can edit players', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php 
                                                    echo wp_kses(JoomSportHelperSelectBox::Radio('seasonadmin[jssa_editplayer]', $is_field_yn,JoomsportSettings::get('jssa_editplayer'),''), JoomsportSettings::getKsesRadio());
                                                ?>

                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo esc_html__('Can edit teams', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php 
                                                    echo wp_kses(JoomSportHelperSelectBox::Radio('seasonadmin[cf_team_cjssa_editteamity_required]', $is_field_yn,JoomsportSettings::get('cf_team_cjssa_editteamity_required'),''), JoomsportSettings::getKsesRadio());
                                                    ?>


                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo esc_html__('Can remove player from season', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php 
                                                    echo wp_kses(JoomSportHelperSelectBox::Radio('seasonadmin[jssa_deleteplayers]', $is_field_yn,JoomsportSettings::get('jssa_deleteplayers'),''), JoomsportSettings::getKsesRadio());
                                                    ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo esc_html__('Can remove teams from season', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php
                                                    echo wp_kses(JoomSportHelperSelectBox::Radio('seasonadmin[jssa_delteam]', $is_field_yn,JoomsportSettings::get('jssa_delteam'),''), JoomsportSettings::getKsesRadio());
                                                    ?>

                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo esc_html__('Can add existing team to season', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php 
                                                    echo wp_kses(JoomSportHelperSelectBox::Radio('seasonadmin[jssa_addexteam]', $is_field_yn,JoomsportSettings::get('jssa_addexteam'),''), JoomsportSettings::getKsesRadio());
                                                    ?>
                                                </td>
                                        </tr>
                                </table>

                            </div>    
                        </div>
                    </div>
                    <div class="jsrespdiv6 jsrespmarginleft2">
                        <div class="jsBepanel">
                            <div class="jsBEheader">
                                <?php echo esc_html__('Single tournament', 'joomsport-sports-league-results-management');?>
                            </div>
                            <div class="jsBEsettings">

                                <table class="adminlistsNoBorder">

                                        <tr>
                                                <td><?php echo esc_html__('Can add existing participant to season', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php 
                                                    echo wp_kses(JoomSportHelperSelectBox::Radio('seasonadmin[jssa_addexteam_single]', $is_field_yn,JoomsportSettings::get('jssa_addexteam_single'),''), JoomsportSettings::getKsesRadio());
                                                    ?>

                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo esc_html__('Can edit participant', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php
                                                    echo wp_kses(JoomSportHelperSelectBox::Radio('seasonadmin[jssa_editplayer_single]', $is_field_yn,JoomsportSettings::get('jssa_editplayer_single'),''), JoomsportSettings::getKsesRadio());
                                                    ?>

                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo esc_html__('Can remove participant from season', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php
                                                    echo wp_kses(JoomSportHelperSelectBox::Radio('seasonadmin[jssa_deleteplayers_single]', $is_field_yn,JoomsportSettings::get('jssa_deleteplayers_single'),''), JoomsportSettings::getKsesRadio());
                                                    ?>
                                                </td>
                                        </tr>
                                </table>

                            </div>
                        </div>
                    </div>
                <div style="clear:both;" ></div>
            </div>
                <div  id="layout_conf_div" class="tabdiv visuallyhidden" >
                <div class="jsrespdiv6">
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('Team page', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            
                            <?php
                            $stdoptions = '';
                             $stdoptions = "std"; 
                            ?>
                            <table class="adminlistsNoBorder">
                                <tr>
                                    <td width="250"><?php echo esc_html__('Order players by', 'joomsport-sports-league-results-management');?></td>
                                    <td>
                                        <?php echo wp_kses(JoomSportHelperSelectBox::Optgroup('layouts[pllist_order]', $alltmp,JoomsportSettings::get('pllist_order')), JoomsportSettings::getKsesSelect());?>

                                    </td>
                                </tr>
                            </table>

                            <h4>
                                <?php echo esc_html__('Player Stats tab settings', 'joomsport-sports-league-results-management');?>
                            </h4>
                            <table class="adminlistsNoBorder">
                                
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Display Player Stats tab', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        
                                        <?php 
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[show_playerstattab]', $is_field_yn,JoomsportSettings::get('show_playerstattab','1'),''), JoomsportSettings::getKsesRadio());
                                        ?>
                                        
                                    </td>

                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Show empty players tab', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[show_playertab]', $is_field_yn,JoomsportSettings::get('show_playertab'),''), JoomsportSettings::getKsesRadio());
                                        ?>

                                    </td>

                                </tr>
                                
                            </table>
                            <h4>
                                <?php echo esc_html__('Roster tab settings', 'joomsport-sports-league-results-management');?>
                            </h4>
                            <table class="adminlistsNoBorder">
                            
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Display Roster tab', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        
                                        <?php 
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[show_rostertab]', $is_field_yn,JoomsportSettings::get('show_rostertab','1'),''), JoomsportSettings::getKsesRadio());
                                        ?>
                                        
                                    </td>

                                </tr>
                                
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Group players by', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                        echo wp_kses(JoomSportHelperSelectBox::Simple('layouts[set_teampgplayertab_groupby]', $adfSel,JoomsportSettings::get('set_teampgplayertab_groupby','0'),'',true), JoomsportSettings::getKsesSelect());
                                        ?>
                                        
                                    </td>

                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Field for number', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <div class="fieldDivPlNum">
                                        <?php
                                        echo wp_kses(JoomSportHelperSelectBox::Simple('layouts[set_playerfieldnumber]', $adfText,JoomsportSettings::get('set_playerfieldnumber','0'),'',true), JoomsportSettings::getKsesSelect());
                                        ?>
                                        </div>  
                                        <div class="fieldDivPlNumSys" style="margin:6px 0px;">
                                            <?php echo esc_html__('System player number', 'joomsport-sports-league-results-management');?>
                                            
                                        </div>
                                        
                                    </td>

                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Extra card field', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                        echo wp_kses(JoomSportHelperSelectBox::Simple('layouts[set_playercardef]', $adfPlayer,JoomsportSettings::get('set_playercardef','0'),'',true), JoomsportSettings::getKsesSelect());
                                        ?>
                                        
                                    </td>

                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Show departed players', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[show_departed]', $is_field_yn,JoomsportSettings::get('show_departed','0')), JoomsportSettings::getKsesRadio());
                                        ?>

                                    </td>

                                </tr>

                            </table>
                            <h4>
                                <?php echo esc_html__('Overview tab settings', 'joomsport-sports-league-results-management');?>
                            </h4>
                            <table class="adminlistsNoBorder">

                                <tr>
                                        <td width="250">
                                            <?php echo esc_html__('Display standings position', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                <tr>
                                        <td>
                                            <?php echo esc_html__('Display team form block', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                <tr>
                                        <td width="200">
                                            <?php echo esc_html__('Display match results block', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                <tr>
                                        <td width="200">
                                            <?php echo esc_html__('Display next matches block', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>

                                <tr>
                                    <td width="200">
                                        <?php echo esc_html__('Show featured image', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enabl_team_featimg]', $is_field_yn,JoomsportSettings::get('enabl_team_featimg', 1)), JoomsportSettings::getKsesRadio());
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>    
                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('Players page', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            <?php
                            $stdoptions = '';
                             $stdoptions = "std"; 
                            ?>
                            <table class="adminlistsNoBorder">

                                <tr>
                                        <td width="250">
                                            <?php echo esc_html__('Enable Career block', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                <tr>
                                        <td width="250">
                                            <?php echo esc_html__('Career fields', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                

                                <tr>
                                        <td>
                                            <?php echo esc_html__('Enable match statistics block', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                
                            </table>
                            

                        </div>    
                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('Calendar page', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            <table class="adminlistsNoBorder">

                                <tr>
                                        <td width="250">
                                            <?php echo esc_html__('Display venue', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[cal_venue]', $is_field_yn,JoomsportSettings::get('cal_venue',1),''), JoomsportSettings::getKsesRadio());
                                            
                                            ?>

                                        </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo esc_html__('Enable player list button', 'joomsport-sports-league-results-management');?>
                                    <td>
                                        <?php 
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_linktoplayerlistcal]', $is_field_yn,JoomsportSettings::get('enbl_linktoplayerlistcal',1),''), JoomsportSettings::getKsesRadio());
                                        ?>


                                    </td>
                                </tr>
                                <?php
                                $stdoptions = '';
                                 $stdoptions = "std"; 
                                ?>
                                <tr>
                                    <td>
                                        <?php echo esc_html__('Enable matches search', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_calmatchsearch]', $is_field_yn,JoomsportSettings::get('enbl_calmatchsearch',1)), JoomsportSettings::getKsesRadio());
                                        
                                        ?>


                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo esc_html__('Matchday name on calendar', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_mdnameoncalendar]', $is_field_yn,JoomsportSettings::get('enbl_mdnameoncalendar',1),''), JoomsportSettings::getKsesRadio());
                                        ?>


                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo esc_html__('Choose layout', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        $is_jscalendar_theme = array();
                                        $is_jscalendar_theme[] = JoomSportHelperSelectBox::addOption(0, __("All matches layout", "joomsport-sports-league-results-management"));
                                        $is_jscalendar_theme[] = JoomSportHelperSelectBox::addOption(1, __("Matches by Matchday layout", "joomsport-sports-league-results-management"));
                                        echo wp_kses(JoomSportHelperSelectBox::Simple('layouts[jscalendar_theme]', $is_jscalendar_theme,JoomsportSettings::get('jscalendar_theme','0'),'',FALSE), JoomsportSettings::getKsesSelect());
        
                                        ?>


                                    </td>
                                </tr>
                                

                            </table>
                        </div>
                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('Season standings page', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            <table class="adminlistsNoBorder">
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Enable player list button', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_linktoplayerlist]', $is_field_yn,JoomsportSettings::get('enbl_linktoplayerlist',1),''), JoomsportSettings::getKsesRadio());
                                        ?>


                                    </td>
                                </tr>
                            </table>
                            <table class="adminlistsNoBorder">
                                <thead>
                                    <tr>
                                        <th>
                                            <?php echo esc_html__('Standings Column', 'joomsport-sports-league-results-management');?>
                                        </th>
                                        <th>
                                            <?php echo esc_html__('Shorten name', 'joomsport-sports-league-results-management');?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $columnshort = json_decode(JoomsportSettings::get('columnshort'),true);
                                
                                foreach($lists['available_options'] as $key => $val){
                                    $currentValue = (isset($columnshort[$key]) && $columnshort[$key])?$columnshort[$key]:$val['short'];
                                    echo '<tr>';
                                    echo '<td width="250">'.esc_html($val['label']).'</td>';
                                    echo '<td><input type="text" name="layouts[columnshort]['.esc_attr($key).']" value="'.esc_attr($currentValue).'" /></td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tbody>    
                            </table>
                            
                        </div>
                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('Match page', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            <table class="adminlistsNoBorder">
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Order lineups by', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php echo wp_kses(JoomSportHelperSelectBox::Optgroup('layouts[pllist_order_se]', $alltmp_se,JoomsportSettings::get('pllist_order_se')), JoomsportSettings::getKsesSelect());?>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Lineups first column', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php echo wp_kses(JoomSportHelperSelectBox::Optgroup('layouts[jsmatch_squad_firstcol]', $lists['adf_player_squad'],JoomsportSettings::get('jsmatch_squad_firstcol')), JoomsportSettings::getKsesSelect());?>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Lineup additional field', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php echo wp_kses(JoomSportHelperSelectBox::Optgroup('layouts[jsmatch_squad_lastcol]', $lists['adf_player_squad'],JoomsportSettings::get('jsmatch_squad_lastcol')), JoomsportSettings::getKsesSelect());?>
                                        
                                    </td>
                                </tr>
                                
                                
                                <tr>
                                    <td>
                                        <?php echo esc_html__('Reverse Home/Away', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[partdisplay_awayfirst]', $is_field_yn,JoomsportSettings::get('partdisplay_awayfirst'),''), JoomsportSettings::getKsesRadio());
                                        ?>


                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo esc_html__('Default match duration', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                        ?>
                                            

                                    </td>
                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Events to kick player out of the match', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                        $kick_events = JoomsportSettings::get('kick_events',array());

                                        if($kick_events){
                                            $kick_events = json_decode($kick_events,true);
                                        }
                                        if(count($eventsOnly)){
                                            echo '<select name="layouts[kick_events][]"  class="jswf-chosen-select" data-placeholder="'.esc_attr(__('Add item','joomsport-sports-league-results-management')).'" multiple>';
                                            foreach ($eventsOnly as $tm) {
                                                $selected = '';
                                                if(in_array($tm->id, $kick_events)){
                                                    $selected = ' selected';
                                                }
                                                echo '<option value="'.esc_attr($tm->id).'" '.esc_attr($selected).'>'.esc_html($tm->name).'</option>';
                                            }
                                            echo '</select>';
                                        }

                                        ?>

                                    </td>

                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Events related to opposite team (e.g. own goal)', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        $opposite_events = JoomsportSettings::get('opposite_events',array());

                                        if($opposite_events){
                                            $opposite_events = json_decode($opposite_events,true);
                                        }
                                        if(count($eventsOnly)){
                                            echo '<select name="layouts[opposite_events][]"  class="jswf-chosen-select" data-placeholder="'.esc_attr(__('Add item','joomsport-sports-league-results-management')).'" multiple>';
                                            foreach ($eventsOnly as $tm) {
                                                $selected = '';
                                                if(in_array($tm->id, $opposite_events)){
                                                    $selected = ' selected';
                                                }
                                                echo '<option value="'.esc_attr($tm->id).'" '.esc_attr($selected).'>'.esc_html($tm->name).'</option>';
                                            }
                                            echo '</select>';
                                        }
                                        
                                        ?>

                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <?php echo esc_html__('Enable matchday name', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                        echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_mdnameonmatch]', $is_field_yn,JoomsportSettings::get('enbl_mdnameonmatch',1),''), JoomsportSettings::getKsesRadio());
                                        ?>


                                    </td>
                                </tr>
                            </table>
                            <h4><?php echo esc_html__('Upcoming match', 'joomsport-sports-league-results-management');?></h4>
                            <table class="adminlistsNoBorder">
                                <tr>
                                    <td width="250">
                                        <?php echo esc_html__('Analytics blocks', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php ?>
                                        <?php  echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>';  ?>




                                    </td>
                                </tr>
                                <tr>
                                    <td width="250" class="hideAnalyticsParts">
                                        <?php echo esc_html__('Events for average block', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td class="hideAnalyticsParts">
                                        <?php

                                        $avgevents_events = JoomsportSettings::get('avgevents_events',array());

                                        if($avgevents_events){
                                            $avgevents_events = json_decode($avgevents_events,true);
                                        }

                                        if(count($Allevents)){
                                            echo '<select name="layouts[avgevents_events][]"  class="jswf-chosen-select" data-placeholder="'.esc_attr(__('Add item','joomsport-sports-league-results-management')).'" multiple>';
                                            foreach ($Allevents as $tm) {
                                                $selected = '';
                                                if(in_array($tm->id, $avgevents_events)){
                                                    $selected = ' selected';
                                                }
                                                echo '<option value="'.esc_attr($tm->id).'" '.esc_attr($selected).'>'.esc_html($tm->name).'</option>';
                                            }
                                            echo '</select>';
                                        }
                                        ?>

                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('Player list page', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            <?php
                            $stdoptions = '';
                             $stdoptions = "std"; 
                            ?>
                            <table class="adminlistsNoBorder">
                                <tr>
                                    <td width="250"><?php echo esc_html__('Order players by', 'joomsport-sports-league-results-management');?></td>
                                    <td>
                                        <?php echo wp_kses(JoomSportHelperSelectBox::Optgroup('layouts[pllistpage_order]', $alltmp,JoomsportSettings::get('pllistpage_order')), JoomsportSettings::getKsesSelect());?>

                                    </td>
                                </tr>
                            </table>
                            

                        </div>    
                    </div>

                    </div>
                    <div class="jsrespdiv6 jsrespmarginleft2">
                        <div class="jsBepanel">
                            <div class="jsBEheader">
                                <?php echo esc_html__('Image settings', 'joomsport-sports-league-results-management');?>
                            </div>
                            <div class="jsBEsettings">
                                <table class="adminlistsNoBorder">
                                    <tr>
                                            <td width="250">
                                                <?php echo esc_html__('Logo height for all lists', 'joomsport-sports-league-results-management');?>
                                            <td>

                                                <input type="text" maxlength="5" name="layouts[teamlogo_height]" style="width:50px;" value="<?php echo esc_attr(JoomsportSettings::get('teamlogo_height',40));?>" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
                                            </td>
                                    </tr>
                                    <tr>
                                            <td>
                                                <?php echo esc_html__('Participant logo height for match page', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>

                                                <input type="text" maxlength="5" name="layouts[set_emblemhgonmatch]" style="width:50px;" value="<?php echo esc_attr(JoomsportSettings::get('set_emblemhgonmatch',140));?>" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
                                            </td>
                                    </tr>
                                    <tr>
                                            <td>
                                                <?php echo esc_html__('Default photo width', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>

                                                <input type="text" maxlength="5" name="layouts[set_defimgwidth]" style="width:50px;" value="<?php echo esc_attr(JoomsportSettings::get('set_defimgwidth',250));?>" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
                                            </td>
                                    </tr>
                                </table>   
                            </div>
                        </div>
                        <div class="jsBepanel">
                            <div class="jsBEheader">
                                <?php echo esc_html__('Players settings', 'joomsport-sports-league-results-management');?>
                            </div>
                            <div class="jsBEsettings">
                                <table class="adminlistsNoBorder">
                                    <tr>
                                            <td width="250">
                                                <?php echo esc_html__('Enable links for player logos', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>
                                                <?php 
                                                echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_playerlogolinks]', $is_field_yn,JoomsportSettings::get('enbl_playerlogolinks',1),''), JoomsportSettings::getKsesRadio());
                                                ?>

                                            </td>
                                    </tr>
                                    <tr>
                                            <td width="250">
                                                <?php echo esc_html__('Enable links for player names', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>
                                                <?php 
                                                echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_playerlinks]', $is_field_yn,JoomsportSettings::get('enbl_playerlinks',1),''), JoomsportSettings::getKsesRadio());
                                                ?>

                                            </td>
                                    </tr>
                                    <tr>
                                        <td width="250" class="hdn_div_enblink_player">
                                            <?php echo esc_html__('Enable links for player from highlighted teams only', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td class="hdn_div_enblink_player">
                                            <?php
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_playerlinks_hglteams]', $is_field_yn,JoomsportSettings::get('enbl_playerlinks_hglteams',0),''), JoomsportSettings::getKsesRadio());
                                            ?>

                                        </td>
                                    </tr>
                                    <tr>
                                            <td width="250">
                                                <?php echo esc_html__('Display players as', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>
                                                <?php 
                                                $listsPl = array();
                                                $listsPl[] = JoomSportHelperSelectBox::addOption(0, __('Name','joomsport-sports-league-results-management'));
                                                $listsPl[] = JoomSportHelperSelectBox::addOption(1, __('Short name','joomsport-sports-league-results-management'));
                                                $listsPl[] = JoomSportHelperSelectBox::addOption(2, __('First name + Last name','joomsport-sports-league-results-management'));
                                                
                                                
                                                echo wp_kses(JoomSportHelperSelectBox::Simple('layouts[players_display_name]', $listsPl,JoomsportSettings::get('players_display_name',0), '', false), JoomsportSettings::getKsesSelect());?>
                                               

                                            </td>
                                    </tr>
                                    <tr>
                                            <td width="250">
                                                <?php echo esc_html__('Enable system player Number connected to Season and Team', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>
                                                <?php 
                                                echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_player_system_num]', $is_field_yn,JoomsportSettings::get('enbl_player_system_num',0),''), JoomsportSettings::getKsesRadio());
                                                ?>

                                            </td>
                                    </tr>
                                    <?php
                                    $stdoptions = '';
                                     $stdoptions = "std"; 
                                    if($stdoptions == 'std'){
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo esc_html__('Show played matches statistic', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[played_matches]', $is_field_yn,JoomsportSettings::get('played_matches'),''), JoomsportSettings::getKsesRadio());
                                            ?>

                                        </td>
                                    </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div> 
                        <div class="jsBepanel">
                            <div class="jsBEheader">
                                <?php echo esc_html__('Team settings', 'joomsport-sports-league-results-management');?>
                            </div>
                            <div class="jsBEsettings">
                                <table class="adminlistsNoBorder">
                                    <tr>
                                            <td width="250">
                                                <?php echo esc_html__('Enable links for team logos', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>
                                                <?php 
                                                echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_teamlogolinks]', $is_field_yn,JoomsportSettings::get('enbl_teamlogolinks',1),''), JoomsportSettings::getKsesRadio());
                                                ?>

                                            </td>
                                    </tr>
                                    <tr>
                                            <td width="250">
                                                <?php echo esc_html__('Enable links for team names', 'joomsport-sports-league-results-management');?>
                                            </td>    
                                            <td>
                                                <?php 
                                                echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_teamlinks]', $is_field_yn,JoomsportSettings::get('enbl_teamlinks',1),''), JoomsportSettings::getKsesRadio());
                                                ?>

                                            </td>
                                    </tr>
                                    <tr>
                                        <td width="250" class="hdn_div_enblink">
                                            <?php echo esc_html__('Enable links for highlighted team only', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td class="hdn_div_enblink">
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Radio('layouts[enbl_teamhgllinks]', $is_field_yn,JoomsportSettings::get('enbl_teamhgllinks'),''), JoomsportSettings::getKsesRadio());
                                            ?>

                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td width="250">
                                            <?php echo esc_html__('Show shortened name extra field for mobiles', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo wp_kses(JoomSportHelperSelectBox::Simple('layouts[shortenteam]', $is_data_shortened,JoomsportSettings::get('shortenteam','-1'),'',true), JoomsportSettings::getKsesSelect());
        
                                            ?>

                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div> 
                        
                </div>
                <div style="clear:both;"></div>
            </div>  

            <div>
                <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce(basename(__FILE__)))?>"/>
                <input name="save" class="button-primary" type="submit" value="<?php echo esc_attr(__("Save changes",'joomsport-sports-league-results-management'));?>">
            </div>
            </form>
        </div>
        </div>    
        <?php
    }
}
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once __DIR__.DIRECTORY_SEPARATOR.'match_types'.DIRECTORY_SEPARATOR.'joomsport-class-match-round-single.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'match_types'.DIRECTORY_SEPARATOR.'joomsport-class-match-round-team.php';
class JoomSportClassMatch{
    public $_mID = null;

    public function __construct($mID) {
        $this->_mID = $mID;
    }

    public function getScore(){
        global $wpdb;
        $args = array("id" => $this->_mID);
        $md = wp_get_post_terms($this->_mID,'joomsport_matchday');
        $mdID = $md[0]->term_id;
        $metas = JoomsportTermsMeta::getTermMeta($mdID);
        $knock = $args["knock"] = $metas['matchday_type'];
        $home_team  = $args["home_team"] = get_post_meta( $this->_mID, '_joomsport_home_team', true );
        $away_team= $args["away_team"] = get_post_meta( $this->_mID, '_joomsport_away_team', true );
        $home_score = $args["home_score"] = get_post_meta( $this->_mID, '_joomsport_home_score', true );
        $away_score = $args["away_score"] = get_post_meta( $this->_mID, '_joomsport_away_score', true );
        
        $hTeam = $args["hTeam"] = $home_team ? get_the_title($home_team) : __('Undefined','joomsport-sports-league-results-management');
        $aTeam = $args["aTeam"] = $away_team ? get_the_title($away_team) : __('Undefined','joomsport-sports-league-results-management');
        $season_id = $args["season_id"] = JoomSportHelperObjects::getMatchSeason($this->_mID);
        $stages = get_post_meta($season_id,'_joomsport_season_stages',true);
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        
        $season_options = get_post_meta($season_id,'_joomsport_season_point',true);


        $enabla_extra = (isset($season_options['s_enbl_extra']) && $season_options['s_enbl_extra']) ? 1:0;
        
        $maps = get_post_meta($this->_mID, '_joomsport_match_maps',true);
        $jmscore = get_post_meta($this->_mID, '_joomsport_match_jmscore',true);


        $sportID = JoomSportHelperObjects::getSportType($season_id);
        $sportTemplClass = JoomSportHelperObjects::getSportTemplate($sportID);

        if(class_exists($sportTemplClass) && method_exists($sportTemplClass,'getScoreMatchBE')){
            $sportTemplClass::getScoreMatchBE($args);
        }
        ?>

        <div class="jstable jsminwdhtd">
            <div class="jstable-row">
                <div class="jstable-cell" style="width:200px;"></div>
                <div class="jstable-cell" style="color:#aaa;">
                    <?php echo esc_html__('Home','joomsport-sports-league-results-management');?>
                </div>
                <div class="jstable-cell"></div>
                <div class="jstable-cell" style="color:#aaa;">
                    <?php echo esc_html__('Away','joomsport-sports-league-results-management');?>
                </div>
            </div>

            
            <?php 
            if($enabla_extra){
                ?>
            <div class="jstable-row">
                <div class="jstable-cell">
                    <?php echo esc_html__('Extra Time','joomsport-sports-league-results-management');?>
                </div>
                <div class="jstable-cell">
                    <?php echo wp_kses(JoomSportHelperSelectBox::Radio('jmscore[is_extra]', $is_field,isset($jmscore['is_extra'])?esc_attr($jmscore['is_extra']):0,''), JoomsportSettings::getKsesRadio());?>
                </div>
                <div class="jstable-cell"></div>
                <div class="jstable-cell"></div>
            </div>
            <div class="jstable-row">
                <div class="jstable-cell js_match_et_addit">
                    <?php echo esc_html__('Score in Extra time','joomsport-sports-league-results-management');?>
                </div>
                <div class="jstable-cell js_match_et_addit">
                    <?php echo esc_html($hTeam)?>
                </div>
                <div class="jstable-cell js_match_et_addit">
                    <input type="number" class="form-control" style="width:50px;" name="jmscore[aet1]" value="<?php echo isset($jmscore['aet1'])?esc_attr($jmscore['aet1']):'';?>" size="5" maxlength="5" />&nbsp;:&nbsp;<input type="number" class="form-control" style="width:50px;" name="jmscore[aet2]" value="<?php echo isset($jmscore['aet2'])?esc_attr($jmscore['aet2']):'';?>" size="5" maxlength="5"/>
                </div>
                <div class="jstable-cell js_match_et_addit">
                    <?php echo esc_html($aTeam)?>
                </div>
            </div>
            <?php
            }
            ?>
            <?php
            if ($stages && count($stages)) {
                for ($i = 0;$i < count($stages);++$i) {
                    $stage_name = $wpdb->get_var("SELECT m_name FROM {$wpdb->joomsport_maps} WHERE id=".intval($stages[$i]));

                    if($stage_name){
                    ?>
                    <div class="jstable-row">
                        <div class="jstable-cell">
                            <?php echo esc_html($stage_name); ?>
                        </div>
                        <?php 
                        echo '<div class="jstable-cell"><span class="jsSpanHome">'.esc_html($hTeam).'</span></div>';
                        echo "<div class='jstable-cell'><input class='jsScrHmV form-control' type='number' name='t1map[]' style='width:50px;' size='5' value='".(isset($maps[$stages[$i]][0]) ? esc_attr($maps[$stages[$i]][0]) : '')."'  />";
                        echo "&nbsp;:&nbsp;<input class='jsScrAwV form-control' type='number' name='t2map[]' style='width:50px;' size='5' value='".(isset($maps[$stages[$i]][1]) ? esc_attr($maps[$stages[$i]][1]) : '')."' /></div>";
                        echo '<div class="jstable-cell"><span class="jsSpanAway">'.esc_html($aTeam).'</span>';
                        echo "<input type='hidden' name='mapid[]' value='".esc_attr($stages[$i])."'/></div>";
                        ?>
                    </div>
                    <?php
                    }
                }
            }
            ?>
            <?php 
            //disable point options for knockout
            if(!$knock){
            ?>
            <div class="jstable-row">
                <div class="jstable-cell">
                    <?php echo esc_html__('Bonus points','joomsport-sports-league-results-management');?>
                </div>
                <div class="jstable-cell">
                    <?php echo esc_html($hTeam)?>
                </div>
                <div class="jstable-cell">
                    <input type="number" class="form-control" style="width:50px;" name="jmscore[bonus1]" value="<?php echo isset($jmscore['bonus1'])?esc_attr($jmscore['bonus1']):''?>" size="5" maxlength="5" />&nbsp;:&nbsp;<input type="number" class="form-control" style="width:50px;" name="jmscore[bonus2]" value="<?php echo isset($jmscore['bonus2'])?esc_attr($jmscore['bonus2']):''?>" size="5" maxlength="5"/>
                </div>
                <div class="jstable-cell">
                    <?php echo esc_html($aTeam)?>&nbsp;
                </div>
            </div>
            <div class="jstable-row">
                <div class="jstable-cell">
                    <?php echo esc_html__('Enable manual match points','joomsport-sports-league-results-management');?>
                </div>
                <div class="jstable-cell">
                    <?php echo wp_kses(JoomSportHelperSelectBox::Radio('jmscore[new_points]', $is_field,isset($jmscore['new_points'])?esc_attr(intval($jmscore['new_points'])):0,''), JoomsportSettings::getKsesRadio());?>
                </div>
                <div class="jstable-cell"></div>
                <div class="jstable-cell"></div>
            </div>
            <div class="jstable-row">
                <div class="jstable-cell jshideonNP"></div>
                <div class="jstable-cell jshideonNP">
                    <?php echo esc_html($hTeam)?>
                </div>
                <div class="jstable-cell jshideonNP">  
                    <input type="number" class="form-control" style="width:50px;" name="jmscore[points1]" value="<?php echo isset($jmscore['points1'])?esc_attr($jmscore['points1']):''?>" size="5" maxlength="5" />&nbsp;:&nbsp;<input type="number" class="form-control" style="width:50px;" name="jmscore[points2]" value="<?php echo isset($jmscore['points2'])?esc_attr($jmscore['points2']):''?>" size="5" maxlength="5"/>
                </div>
                <div class="jstable-cell jshideonNP">
                    <?php echo esc_html($aTeam)?>&nbsp;
                </div>
            </div>
            <?php
            }
            ?>
        </div>
        <?php
    }

    public function save(){

    }
}
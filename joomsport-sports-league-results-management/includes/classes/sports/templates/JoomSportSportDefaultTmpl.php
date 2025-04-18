<?php
require_once JOOMSPORT_PATH_INCLUDES.'classes'.DIRECTORY_SEPARATOR.'sports'.DIRECTORY_SEPARATOR.'joomsport-sport-common.php';

class JoomSportSportDefaultTmpl extends joomsportSportCommon
{
    public static function getScoreMatchBE($args){
        ?>
        <div class="jstable-row">
            <div class="jstable-cell" style="width:200px;">
                <?php echo esc_html__('Score','joomsport-sports-league-results-management');?>
                <?php if($args["knock"]){echo '<img width="12" class="jsknchange" src="'.esc_url(plugins_url( '../../assets/images/reverse_order.png', __FILE__ )).'">';}; ?>
            </div>

            <?php
            if($args["knock"]){
                ?>
                <div class="jstable-cell" style="width:15%;">
                        <span class="jsSpanHome">
                            <?php echo esc_html($args["hTeam"])?>
                            <input type="hidden" name="knteamid[]" value="<?php echo esc_attr($args["home_team"]);?>" />
                        </span>
                </div>
                <div class="jstable-cell" style="width:20%;">
                        <span class="jsSpanHomeScore" style="width:52px;display: inline-block;text-align: center;">
                            <?php echo esc_html($args["home_score"])?>
                            <input type="hidden" name="knteamscore[]" value="<?php echo esc_attr($args["home_score"]);?>" />
                        </span>
                    &nbsp;:&nbsp;
                    <span class="jsSpanAwayScore" style="width:52px;display: inline-block;text-align: center;">
                            <?php echo esc_html($args["away_score"])?>
                            <input type="hidden" name="knteamscore[]" value="<?php echo esc_attr($args["away_score"]);?>" />
                        </span>
                </div>
                <div class="jstable-cell" >
                        <span class="jsSpanAway">
                            <?php echo esc_html($args["aTeam"])?>
                            <input type="hidden" name="knteamid[]" value="<?php echo esc_attr($args["away_team"]);?>" />
                        </span>
                </div>
                <?php
            }else{
                ?>
                <div class="jstable-cell">
                        <span class="jsSpanHome">
                            <?php echo esc_html($args["hTeam"])?>
                        </span>
                </div>
                <div class="jstable-cell">
                    <input type="number" class="form-control" <?php echo $args["knock"]?' disabled':'';?> style="max-width:50px;" name="score1" value="<?php echo esc_attr($args["home_score"])?>" size="5" maxlength="5" />&nbsp;:&nbsp;<input type="number" class="form-control" <?php echo $args["knock"]?' disabled':'';?> style="max-width:50px;" name="score2" value="<?php echo esc_attr($args["away_score"])?>" size="5" maxlength="5"/>
                </div>
                <div class="jstable-cell">
                        <span class="jsSpanHome">
                            <?php echo esc_html($args["aTeam"])?>
                        </span>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }
    public static function saveScoreMatchBE($args){
        $post = $args["post"];
        $post_id = $args["post_id"];
        $prev_home_score = get_post_meta( $post_id, '_joomsport_home_score', true );
        $prev_away_score = get_post_meta( $post_id, '_joomsport_away_score', true );

        if(!$args['matchday_type']){
            update_post_meta($post_id, '_joomsport_home_score', intval($post['score1']));
            update_post_meta($post_id, '_joomsport_away_score', intval($post['score2']));

            $home_score = intval($post['score1']);
            $away_score = intval($post['score2']);
        }
        if(isset($post['knteamid']) && count($post['knteamid'])){
            update_post_meta($post_id, '_joomsport_home_team', intval($post['knteamid'][0]));
            update_post_meta($post_id, '_joomsport_away_team', intval($post['knteamid'][1]));
            if(isset($post['knteamscore']) && count($post['knteamscore'])){
                update_post_meta($post_id, '_joomsport_home_score', intval($post['knteamscore'][0]));
                update_post_meta($post_id, '_joomsport_away_score', intval($post['knteamscore'][1]));
                $home_score = intval($post['knteamscore'][0]);
                $away_score = intval($post['knteamscore'][1]);
            }
        }
        if($prev_home_score != $home_score || $prev_away_score != $away_score){
            do_action("joomsport_score_changed", $post_id);
        }
    }
    public static function getScoreFE($match){
        $jmscore = get_post_meta($match->id, '_joomsport_match_jmscore',true);
        $width = JoomsportSettings::get('set_emblemhgonmatch', 60);
        $partic_home = $match->getParticipantHome();
        $partic_away = $match->getParticipantAway();
        ?>

        <div class="row">
            <div class="jsMatchTeam jsMatchHomeTeam col-xs-6 col-sm-5 col-md-4">
                <div class="row">
                    <div class="jsMatchEmbl jscenter col-md-5">
                        <?php echo $partic_home ? wp_kses_post($partic_home->getEmblem(true, 0, 'emblInline', $width)) : ''; ?>
                    </div>
                    <div class="jsMatchPartName col-md-7">
                        <div class="row">
                                        <span>
                                            <?php echo ($partic_home) ? wp_kses_post($partic_home->getName(true)) : ''; ?>
                                        </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="jsMatchTeam jsMatchAwayTeam col-xs-6 col-sm-5 col-sm-offset-2 col-md-4 col-md-push-4">
                <div class="row">
                    <div class="jsMatchEmbl jscenter col-md-5 col-md-push-7">
                        <?php echo $partic_away ? wp_kses_post($partic_away->getEmblem(true, 0, 'emblInline', $width)) : ''; ?>
                    </div>
                    <div class="jsMatchPartName col-md-7 col-md-pull-5">
                        <div class="row">
                            <span>
                                <?php echo ($partic_away) ? wp_kses_post($partic_away->getName(true)) : ''; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="jsMatchScore col-xs-12 col-md-4 col-md-pull-4">
                <?php if (isset($jmscore['is_extra']) && $jmscore['is_extra']) {
                    ?>
                    <div class="jsMatchExtraTime">
                        <?php
                        if(isset($jmscore['aet1'])){
                            echo '<span class="aetSmDivScoreH">'.wp_kses_post($jmscore['aet1']).'</span>';
                        }
                        ?>
                        <img  src="<?php echo JOOMSPORT_LIVE_ASSETS?>images/extra-t.png" alt="<?php echo esc_html__('Won in extra time','joomsport-sports-league-results-management');?>" title="<?php echo esc_html__('Won in extra time','joomsport-sports-league-results-management');?>" />
                        <?php
                        if(isset($jmscore['aet2'])){
                            echo '<span class="aetSmDivScoreA">'.wp_kses_post($jmscore['aet2']).'</span>';
                        }
                        ?>
                    </div>
                    <?php
                } ?>
                <?php echo jsHelper::getScoreBigM($match); ?>
            </div>
        </div>
        <?php
    }
    public static function getScoreFESmall($args){
        return $text = $args["home_score"].JSCONF_SCORE_SEPARATOR.$args["away_score"];

    }

    public static function getScoreModuleScoreHome($args){
        echo '<td width="30">';
        echo '<div class="scoreScrMod">'.classJsportLink::match($args["home_score"], $args["match"]->id,false,'').'</div>';
        echo '</td>';
    }
    public static function getScoreModuleScoreAway($args){
        echo '<td width="30">';
        echo '<div class="scoreScrMod">'.classJsportLink::match($args["away_score"], $args["match"]->id,false,'').'</div>';
        echo '</td>';
    }
    public static function getScoreHTMLHelper($args){
        $match = $args["match"];
        $htmlLive = '';
        if($args["m_played"] == -1){
            $liveWrd = __("Live", 'joomsport-sports-league-results-management' );
            $ticker_html = jsHelper::matchTicker($match->id);
            $htmlLive = '<div class="jscalendarLive">'.($ticker_html?$ticker_html:$liveWrd).'</div>';
        }
        return '<div class="jsScoreDiv '.$args["class"].'" data-toggle2="tooltip" data-placement="bottom" title="" data-original-title="'.htmlspecialchars(($args["tooltip"])).'">'.$htmlLive.$args["html"].$match->getETLabel().'</div>'.$match->getBonusLabel();

    }


}
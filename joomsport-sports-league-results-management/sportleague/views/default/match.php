<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$jmscore = get_post_meta($rows->id, '_joomsport_match_jmscore',true);
$m_venue = get_post_meta($rows->id,'_joomsport_match_venue',true);
?>
<div id="jsMatchViewID">
    <div class="jsMatchResultSection">
        <div class="jsMatchHeader clearfix">
            <div class="col-xs-4 col-sm-5">
                <div class="matchdtime row">
                    <?php
                    $m_date = get_post_meta($rows->id,'_joomsport_match_date',true);
                    $m_time = get_post_meta($rows->id,'_joomsport_match_time',true);
                    if ($m_date && $m_date != '0000-00-00') {
                        echo '<img src="'.JOOMSPORT_LIVE_ASSETS.'images/calendar-date.png" alt="" />';
                        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $m_date)) {
                            echo '<span>'. wp_kses_post(classJsportDate::getDate($m_date, $m_time)) .'</span>';
                        } else {
                            echo '<span>'. wp_kses_post($m_date) .'</span>';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="col-xs-4 col-sm-2 jscenter">
                <div>
                    <?php
                    if(JoomsportSettings::get('enbl_mdnameonmatch',1)) {
                        echo '<div class="jsmatchday"><span>' . wp_kses_post($rows->getMdayName()) . '</span></div>';
                    }
                    ?>
                </div>
            </div>
            <div class="col-xs-4 col-sm-5">
                <div class="matchvenue row">
                    <?php
                    if ($m_venue) {
                        if($rows->getLocation()){
                            echo '<div><span>'.wp_kses_post($rows->getLocation()).'</span></div>';
                            echo '<img src="'.JOOMSPORT_LIVE_ASSETS.'images/location.png" alt="'.esc_html__('location','joomsport-sports-league-results-management').'" />';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="jsMatchResults">
            <?php

            $sportID = JoomSportHelperObjects::getSportType($rows->season_id);
            $sportTemplClass = JoomSportHelperObjects::getSportTemplate($sportID);
            if(class_exists($sportTemplClass) && method_exists($sportTemplClass,'getScoreFE')){
                $sportTemplClass::getScoreFE($rows);
            }

                do_action("joomsport_matchpage_after_score", $rows->id, $rows->season_id);
            ?>

            <!-- MAPS -->
            <?php
            if ($rows->lists['maps'] && count($rows->lists['maps'])) {
                echo wp_kses_post(jsHelper::getMap($rows->lists['maps']));
            }
            ?>
        </div>
    </div>
    <?php apply_filters("joomsport_custom_votes", $rows->id);?>
    <div class="jsMatchContentSection clearfix">
        <?php
        $tabs = $rows->getTabs();
        jsHelperTabs::draw($tabs, $rows);
        ?>
    </div>
</div>
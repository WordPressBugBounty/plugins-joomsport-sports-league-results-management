<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
$wp_template_path = get_template_directory();
if(file_exists($wp_template_path . '/joomsport/includes/demo/joomsport-demo.php')){
    include_once( $wp_template_path . '/joomsport/includes/demo/joomsport-demo.php' );
} else {
    include_once( JOOMSPORT_PATH . 'includes/demo/joomsport-demo.php' );
}

class JoomSportSetupDemo {

    public static function init(){
        global $pagenow;
        
        add_action( 'admin_menu', array('JoomSportSetupDemo', 'create_setup_page') );
        
        if($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'joomsport_setup'){
            if(isset($_POST['js_demotype'])){
                JoomSportDemoData::installDemoData(intval($_POST['js_demotype']));
            }
        }
    }

    public static function create_setup_page() {
        add_dashboard_page('JoomSport', '', 'manage_options', 'joomsport_setup', array('JoomSportSetupDemo', 'showSetupPage'));
    }

    public static function showSetupPage(){
        $lists_radio = array();
        $lists_radio[] = JoomSportHelperSelectBox::addOption(0, esc_html__('Single sport','joomsport-sports-league-results-management'));
        $lists_radio[] = JoomSportHelperSelectBox::addOption(1, esc_html__('Team sport','joomsport-sports-league-results-management'));
        $lists_radio[] = JoomSportHelperSelectBox::addOption(2, esc_html__('Both','joomsport-sports-league-results-management'));
        ?>
        <div class="jsportWizzardDiv">

            <form method="post" action="">
                <div class="jsportWizzardDivInner">
                    <h1><?php echo esc_html__('JoomSport setup wizard', 'joomsport-sports-league-results-management');?></h1>
                    <div class="jsportWizzardDivCenter">
                        <label><?php echo esc_html__('Select your sport type', 'joomsport-sports-league-results-management');?></label>
                        <div style="margin-left:90px;">
                            <?php echo wp_kses(JoomSportHelperSelectBox::Radio('js_demotype', $lists_radio,1), JoomsportSettings::getKsesRadio())?>
                        </div>
                    </div>
                    <div>
                        <br />
                        <div class="jsportWizzardDivCenter">
                            <label><?php echo esc_html__('Install basic demo data', 'joomsport-sports-league-results-management');?></label>
                        </div>
                        
                        <fieldset>
                            <?php echo esc_html__("The following items are included",'joomsport-sports-league-results-management')?>:<br /><br />
                            <?php 
                            
                            echo " - ".esc_html__("Leagues",'joomsport-sports-league-results-management');
                            echo "<br />";
                            echo " - ".esc_html__("Seasons",'joomsport-sports-league-results-management');
                            echo "<br />";
                            echo " - ".esc_html__("Teams and Players (depending on sport)",'joomsport-sports-league-results-management');
                            echo "<br />";
                            echo " - ".esc_html__("Round robin Matchdays with Matches",'joomsport-sports-league-results-management');
                            echo "<br />";
                            echo " - ".esc_html__("Basic statistic like Player and Match Event stats",'joomsport-sports-league-results-management');
                            echo "<br />";
                            echo " - ".esc_html__("Box score stats",'joomsport-sports-league-results-management');
                            echo "<br />";
                            ?>
                        </fieldset>
                        
                    </div>
                    <div style="padding-top:25px;">
                        <div style="display: inline-block">
                            <a href="<?php echo admin_url( 'edit-tags.php?taxonomy=joomsport_tournament&post_type=joomsport_season' ) ;?>">
                                <input type="button" class="button button-large" value="<?php echo esc_attr(esc_html__('No, start empty database', 'joomsport-sports-league-results-management'));?>" />
                            </a>
                        </div>
                        <div style="display: inline-block; float: right;">
                            <input type="submit" class="button button-primary button-large" value="<?php echo esc_attr(esc_html__('Yes, install basic demo data', 'joomsport-sports-league-results-management'));?>" />
                        </div>
                    </div>
                </div>   
            </form>    
        </div>    
        <?php
    }
}

add_action( 'init', array( 'JoomSportSetupDemo', 'init' ), 5);
add_action( 'wp_ajax_joomsport_demo_ttype', array("JoomSportSetupDemo",'setTournType') );
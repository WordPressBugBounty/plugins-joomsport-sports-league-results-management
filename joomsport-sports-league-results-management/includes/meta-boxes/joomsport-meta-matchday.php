<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_INCLUDES . 'classes'. DIRECTORY_SEPARATOR . 'joomsport-class-matchday.php';
class JoomSportMetaMatchday {
    
    public static function joomsport_matchday_edit_form_fields($term_obj){
        echo '</table>';
        echo '<div>';
        echo JoomSportClassMatchday::getViewEdit($term_obj->term_id);
        echo '</div>';
        echo '<div id="modalAj"><!-- Place at bottom of page --></div><table>';
    }
    public static function joomsport_matchday_add_form_fields($term_id){
        $results = JoomSportHelperObjects::getSeasons(null, false);
        ?>
        <div class="form-field form-required">    
        
            <label for="season_id"><?php echo esc_html__('Season', 'joomsport-sports-league-results-management'); ?></label>

            <?php
                echo wp_kses(JoomSportHelperSelectBox::Optgroup('season_id', $results,'',' id="season_id" onchange="jsFormMDVal();"',true,''), JoomsportSettings::getKsesSelect());
            ?>
            <input type="number" id="season_id_inp" name="season_id_inp" value="" required="required" aria-required="true" style="visibility: hidden;height:0px;">
            <p>
                <?php echo esc_html__('Please select season related to this matchday', 'joomsport-sports-league-results-management');?>
            </p>
        </div>
        <div class="form-field">    
        
            <label for="md_type"><?php echo esc_html__('Matchday Type', 'joomsport-sports-league-results-management'); ?></label>

                <?php
                $is_field = array();
                $is_field[] = JoomSportHelperSelectBox::addOption(0, __("Round Robin", "joomsport-sports-league-results-management"));
                
                echo wp_kses(JoomSportHelperSelectBox::Simple('matchday_type', $is_field,0,' id="JSMD_matchday_type"',false), JoomsportSettings::getKsesSelect());
                
                ?>

            <p>
                <?php echo esc_html__('Please select matchday type', 'joomsport-sports-league-results-management');?>
                <?php
                    $stdoptions = '';
                     $stdoptions = "std"; 
                    if($stdoptions == 'std'){
                        echo "<br />Knockouts are ";
                    }
                     echo 'Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only'; 
                ?>
            </p>
        </div>
        <div id="jsknock_only" class="form-field" style="display: none;">    
        
            <label for="md_knock_format"><?php echo esc_html__('Select format', 'joomsport-sports-league-results-management'); ?></label>

                <?php
                $is_field = array();
                $iFormat = 2;
                while($iFormat < 257){
                    $is_field[] = JoomSportHelperSelectBox::addOption($iFormat, $iFormat);
                    $iFormat *= 2;
                }

                echo wp_kses(JoomSportHelperSelectBox::Simple('md_knock_format', $is_field,16,'',false), JoomsportSettings::getKsesSelect());
                
                ?>

            <p>
                <?php echo esc_html__('Please select knockout format', 'joomsport-sports-league-results-management');?>
            </p>
        </div>
    <?php
        
    }
    public static function joomsport_matchday_save_form_fields($term_id){
        $generatem = isset($_POST['autogeneration']) && $_POST['autogeneration'] == 'true';
        if(!$generatem){
            if(!isset($_POST['tag_ID']) || !intval($_POST['tag_ID'])){
                //$term_metas = get_option("taxonomy_{$term_id}_metas");
                $term_metas = JoomsportTermsMeta::getTermMeta($term_id);
                if (!is_array($term_metas)) {
                    $term_metas = Array();
                }
                if(isset($_POST['season_id'])){
                    // Save the meta value
                    $term_metas['season_id'] = intval($_POST['season_id']);

                    $term_metas['matchday_type'] = isset($_POST['matchday_type'])?intval($_POST['matchday_type']):0;
                    if(isset($_POST['md_knock_format']) && intval($_POST['md_knock_format'])){
                        $term_metas['knockout_format'] = intval($_POST['md_knock_format']);
                    }

                    //update_option( "taxonomy_{$term_id}_metas", $term_metas );
                    JoomsportTermsMeta::updateTermMeta($term_id, $term_metas);
                }
                //wp_redirect('term.php?taxonomy=joomsport_matchday&tag_ID='.$term_id.'&post_type=joomsport_match');
               // exit();
            }else{
                JoomSportClassMatchday::save($term_id);

                //wp_redirect('term.php?taxonomy=joomsport_matchday&tag_ID='.$term_id.'&post_type=joomsport_match');
                //exit();
            }
        }
        
    }
    public static function matchday_type_columns( $taxonomies ) {
        $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        'header_icon' => '',
//      'description' => __('Description'),
        'season_name' => __('Season'),
        'matchday_type' => __('Type'),
        'posts' => __('Posts')
        );

        return $new_columns;    
    }

 
    public static function manage_joomsport_matchday_columns($out, $column_name, $tax_id) {
        //echo $theme_id;
        //$metas = get_option("taxonomy_{$tax_id}_metas");
        $metas = JoomsportTermsMeta::getTermMeta($tax_id);

        switch ($column_name) {
            case 'season_name': 

                $out .= isset($metas['season_id'])?get_the_title($metas['season_id']):"";
                break;
            case 'matchday_type': 

                $out .= (isset($metas['matchday_type']) && $metas['matchday_type'] ? __("Knockout", "joomsport-sports-league-results-management") : __("Round Robin", "joomsport-sports-league-results-management"));
                break;

            default:
                break;
        }
        return $out;    
    }
}



class JSMday_FILTER

{

    /**

    * The ajax action

    */

    const ACTION = 'jsmday_filter_save';

    /**

    * Our nonce name

    */

    const NONCE = 'jsmday_filter_nonce';
    public static function init()
    {

        add_action('load-edit-tags.php',array(get_class(), 'load'));
        add_action('wp_ajax_' . self::ACTION,array(get_class(), 'ajax'));


    }
    public static function load()

    {
        $screen = get_current_screen();

        // get out of here if we are not on our settings page

        if(!is_object($screen) || $screen->id != 'edit-joomsport_matchday' || $screen->base != 'edit-tags'){
            return;
        }

        add_filter('screen_settings',array(get_class(), 'add_field'),10,2);

        add_action('admin_head',array(get_class(), 'head'));


    }
    public static function add_field($rv, $screen)

    {

        $season_id = get_user_option('filter_season_id',get_current_user_id());

        $rv = '<label for="amount">Season:</label> ';
        $results = JoomSportHelperObjects::getSeasons(null, false);
        ksort($results);
        $rv .= JoomSportHelperSelectBox::Optgroup('filter_season_id', $results,$season_id,' id="filter_season_id"',true,0);

        $rv .= wp_nonce_field(self::NONCE, self::NONCE, false, false);


        return $rv;

    }
    public static function head()

    {

    ?>

        <script type="text/javascript">

        jQuery(document).ready(function() {

            jQuery('#filter_season_id').change(function() {

                jQuery.post(

                    ajaxurl,

                    {

                    title: jQuery(this).val(),

                    nonce: jQuery('input#<?php echo esc_js(self::NONCE); ?>').val(),

                    screen: '<?php echo esc_js(get_current_screen()->id); ?>',

                    action: '<?php echo esc_js(self::ACTION); ?>'

                    }

                );

            });
            jQuery("#tag-name").on("change", function(){
                mdFillSlug();
            });
            jQuery("#season_id").on("change", function(){
                mdFillSlug();
            });
            function mdFillSlug(){
                var str = jQuery("#tag-name").val()+'_'+jQuery("#season_id").val();
                var str = encodeURIComponent(str);
                jQuery("#tag-slug").val(str);
            }

        });

        </script>

    <?php

    }
    public static function ajax()

    {


        check_ajax_referer(self::NONCE, 'nonce');

        $screen = isset($_POST['screen']) ? sanitize_text_field(wp_unslash($_POST['screen'])) : false;


        $title = isset($_POST['title']) ? intval($_POST['title']) : false;

        if(!$screen || !($user = wp_get_current_user()))

        {

        die(0);

        }

        if(!$screen = sanitize_key($screen))

        {

        die(0);

        }

        update_user_option(

        $user->ID,

        "filter_season_id",

        esc_attr(wp_strip_all_tags($title))

        );

        die('1');

    }

} // end class

JSMday_FILTER::init();



add_filter( 'get_terms_args', 'jsmday_filter_get_terms_args', 10, 2 );
/**
 * Exclude categories from "Edit Categories" screen
 *
 */
 function jsmday_filter_get_terms_args( $args, $taxonomies ) {

    if(!is_admin()){
        return $args;
    } 
    if(!function_exists('get_current_screen')){
        return $args;
    }    
    $screen = get_current_screen();
     $season_id = get_user_option('filter_season_id',get_current_user_id());
	// get out of here if we are not on our settings page
	if(!is_object($screen) || $screen->id != 'edit-joomsport_matchday' || !$season_id)
		return $args;
        if($screen->base != 'edit-tags'){
            return $args;
        }

        if($taxonomies[0] != 'joomsport_matchday'){
            return $args;
        }
        if(isset($_REQUEST["action"]) && $_REQUEST["action"] == 'delete'){
            return $args;
        }

	$filtered_terms = array();
        $NOTfiltered_terms = array();
        remove_filter( 'get_terms_args', 'jsmday_filter_get_terms_args' );
        $terms = get_terms(array(
            'taxonomy' => 'joomsport_matchday',
            'hide_empty' => false,
        ));
        add_filter( 'get_terms_args', 'jsmday_filter_get_terms_args', 10, 2 );
	foreach ( $terms as $term )
	{
            //$metas = get_option("taxonomy_{$term->term_id}_metas");
            $metas = JoomsportTermsMeta::getTermMeta($term->term_id);
            if(isset($metas['season_id'])){
                $_seasonID = $metas['season_id'];
                //echo $season_id .'=='. $_seasonID."<br />";
                if($season_id == $_seasonID){
			$filtered_terms[] = $term->term_id;
                }else{
                    $NOTfiltered_terms[] = $term->term_id;
                }        
            }
	}
	
        if(!count($filtered_terms)){
            $filtered_terms = array(-1);
        }
    $args['include'] = $filtered_terms; // Array of cat ids you want to exclude
    //$args['exclude'] = $NOTfiltered_terms;
    //var_dump($args);
    return $args;
}

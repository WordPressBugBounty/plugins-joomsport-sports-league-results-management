<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

require_once JOOMSPORT_PATH_INCLUDES . 'classes'. DIRECTORY_SEPARATOR . 'joomsport-class-matchday.php';

class JoomSportTaxonomyMatchday {
    public function __construct() {

    }
    public static function init(){
        self::register_taxonomy();
    }
    public static function register_taxonomy(){
        $slug = get_option( 'joomsportslug_joomsport_matchday', null );
        $labels = array(
                'name' => __( 'Matchdays', 'joomsport-sports-league-results-management' ),
                'singular_name' => __( 'Matchday', 'joomsport-sports-league-results-management' ),
                'all_items' => __( 'All', 'joomsport-sports-league-results-management' ),
                'edit_item' => __( 'Edit Matchday', 'joomsport-sports-league-results-management' ),
                'view_item' => __( 'View', 'joomsport-sports-league-results-management' ),
                'update_item' => __( 'Update', 'joomsport-sports-league-results-management' ),
                'add_new_item' => __( 'Add New', 'joomsport-sports-league-results-management' ),
                'new_item_name' => __( 'Name', 'joomsport-sports-league-results-management' ),
                'parent_item' => __( 'Parent', 'joomsport-sports-league-results-management' ),
                'parent_item_colon' => __( 'Parent:', 'joomsport-sports-league-results-management' ),
                'search_items' =>  __( 'Search', 'joomsport-sports-league-results-management' ),
                'not_found' => __( 'No results found.', 'joomsport-sports-league-results-management' ),
        );
        $args = array(
                'label' => __( 'Matchdays', 'joomsport-sports-league-results-management' ),
                'labels' => $labels,
                'public' => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
                'hierarchical' => false,
                'rewrite' => array(
                    'slug' => $slug?$slug:'joomsport_matchday'
                )
                   
        );
        $object_types = apply_filters( 'joomsport_matchday_object_types', array( 'joomsport_match' ) );
        register_taxonomy( 'joomsport_matchday', $object_types, $args );
        foreach ( $object_types as $object_type ):
                register_taxonomy_for_object_type( 'joomsport_matchday', $object_type );
        endforeach;
        
        $tournament_tax = new JoomSportTaxonomyDropM('joomsport_matchday', __( 'Matchday', 'joomsport-sports-league-results-management' ), array('joomsport_match'));
        add_action('add_meta_boxes', array( $tournament_tax, 'joomsport_custom_meta_box'));
        add_action( 'save_post', array( $tournament_tax, 'taxonomy_save_postdata') );
        
        require_once JOOMSPORT_PATH_INCLUDES . 'meta-boxes' . DIRECTORY_SEPARATOR . 'joomsport-meta-matchday.php';
        add_filter( 'manage_edit-joomsport_matchday_columns', array( 'JoomSportMetaMatchday','matchday_type_columns') );
        add_action("manage_joomsport_matchday_custom_column", array( 'JoomSportMetaMatchday','manage_joomsport_matchday_columns'), 10, 3);
        add_action('joomsport_matchday_edit_form_fields',array( 'JoomSportMetaMatchday', 'joomsport_matchday_edit_form_fields'));
        add_action('joomsport_matchday_add_form_fields',array( 'JoomSportMetaMatchday', 'joomsport_matchday_add_form_fields'));
        add_action('edited_joomsport_matchday', array( 'JoomSportMetaMatchday', 'joomsport_matchday_save_form_fields'), 10, 2);
        add_action('created_joomsport_matchday', array( 'JoomSportMetaMatchday', 'joomsport_matchday_save_form_fields'), 10, 2);
        
        add_action( 'wp_ajax_mday_savematch', array("JoomSportTaxonomyMatchday",'joomsport_mday_savematch') );
        add_action( 'wp_ajax_mday_saveknock', array("JoomSportTaxonomyMatchday",'joomsport_mday_saveknock') );

    }

    public static function joomsport_mday_savematch(){
        if(isset($_POST['formdata'])){parse_str(sanitize_text_field(wp_unslash($_POST['formdata'])), $output);}
        if(isset($output["tag_ID"])){

            JoomSportClassMatchday::saveMatch(intval($output["tag_ID"]));
            
        }
        
        wp_die();
    }
    public static function joomsport_mday_saveknock(){
        if(isset($_POST['formdata'])){parse_str(sanitize_text_field(wp_unslash($_POST['formdata'])), $output);}
        if(isset($output["tag_ID"]) && current_user_can('manage_options')){

            JoomSportClassMatchday::save(intval($output["tag_ID"]));
            
        }
        
        wp_die();
    }

    public static function generate_button(){
         echo '<a href="admin.php?page=joomsport-match-generator"><input type="button" class="button button-primary" value="'.esc_attr(__('Generate matches','joomsport-sports-league-results-management')).'"></a>';

    }

}    

// class ovveride taxonomy to dropdown

class JoomSportTaxonomyDropM{
    public $pages = array();
    public $name = null;
    public $name_slug = null;
    
    public function __construct($name, $name_slug, $pages){
        $this->name = $name;
        $this->name_slug = $name_slug;
        $this->pages = $pages;
    }
    
    public function joomsport_custom_meta_box() {

        remove_meta_box( 'tagsdiv-'.$this->name, 'joomsport_match', 'side' );

        add_meta_box( 'tagsdiv-'.$this->name, $this->name_slug, array( $this, 'drop_meta_box'), 'joomsport_match', 'side' );

    }
    
    public function drop_meta_box($post) {

        $taxonomy = get_taxonomy($this->name_slug);
        $md = wp_get_post_terms($post->ID,'joomsport_matchday');
        $mdID = $md[0]->term_id;

        $metas = JoomsportTermsMeta::getTermMeta($mdID);
        $season_id = $metas['season_id'];
        
        ?>
        <div class="tagsdiv" id="<?php echo esc_attr($this->name); ?>">
            <div class="jaxtag">
            <?php 
            wp_nonce_field( plugin_basename( __FILE__ ), $this->name.'_noncename' );
            $type_IDs = wp_get_object_terms( $post->ID, $this->name, array('fields' => 'ids') );
            
            $current_tournament = !isset($type_IDs[0]) ? 0 : $type_IDs[0];

            $tx = JoomsportTermsMeta::getTerms('joomsport_matchday', array("hide_empty" => false), array("season_id" => $season_id, "matchday_type" => 0));

            if(intval($metas['matchday_type']) > 0){
                echo esc_html($md[0]->name);
            }else{
                echo '<select name="joomsport_matchday" id="joomsport_matchday_inseas_id" class="postform" aria-required="true">';
                    echo '<option value="-1">'.esc_html__('Select Matchday','joomsport-sports-league-results-management').'</option>';
                    for($intA=0;$intA<count($tx);$intA++){
                        echo '<option value="'.esc_attr($tx[$intA]->term_id).'" '.($tx[$intA]->term_id == $current_tournament?'selected':'').'>'.esc_html($tx[$intA]->name).'</option>';
                    }

                echo '</select>';
            }
            //echo '<select>'
            //wp_dropdown_categories('taxonomy='.$this->name.'&t_single=1&hide_empty=0&orderby=name&name='.$this->name.'&show_option_none=Select '.$this->name_slug.'&selected='.$current_tournament); ?>
            </div>
        </div>
        <?php
    }
     public  function taxonomy_save_postdata( $post_id ) {

      if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) 
          return;
      if(!isset($_POST[$this->name.'_noncename']))
          return;    
      if ( !wp_verify_nonce( sanitize_text_field(wp_unslash($_POST[$this->name.'_noncename'])), plugin_basename( __FILE__ ) ) )
          return;
         $pstTYpe = isset($_POST['post_type'])?sanitize_text_field(wp_unslash($_POST['post_type'])):'';

      if ( 'joomsport_match' == $pstTYpe )
      {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
      }
      else
      {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
      }
      if(isset($_POST[$this->name])){
        $type_ID = isset($_POST[$this->name])?intval($_POST[$this->name]):0;

        $type = ( $type_ID > 0 ) ? get_term( $type_ID, $this->name )->slug : NULL;

        wp_set_object_terms(  $post_id , $type, $this->name );
      }
    }
    
    
    
}
function joomsport_filter_matchday_terms_order( $orderby, $query_vars, $taxonomies ) {
    if(is_array($taxonomies) && $taxonomies[0] == 'joomsport_matchday'){
        return 't.term_order, t.term_id';
    }
    return $orderby;
}

add_filter( 'get_terms_orderby', 'joomsport_filter_matchday_terms_order', 10, 3 );



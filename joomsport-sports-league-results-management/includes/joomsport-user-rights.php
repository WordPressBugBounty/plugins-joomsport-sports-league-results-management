<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */


class JoomSportUserRights {

    public static function jsp_add_theme_caps() {
        add_role( 'joomsport_moderator', __( 'JoomSport Moderator', 'joomsport-sports-league-results-management'), array( 'read' => true, 'level_0' => true ) );
        $moder = get_role( 'joomsport_moderator' );
        $moder->add_cap( 'read' ); 
        $moder->add_cap('level_0');
        $moder->remove_cap('edit_posts');


        $roles = array('administrator','joomsport_moderator');
        for($intA=0; $intA < count($roles); $intA++){

            $admins = get_role( $roles[$intA] );
            
            $admins->add_cap( 'jsp_matchday_manage');
            $admins->add_cap( 'jsp_matchday_edit');
            
            $admins->add_cap( 'edit_jscp_player' ); 
            $admins->add_cap( 'edit_jscp_players' ); 
            $admins->add_cap( 'edit_others_jscp_player' ); 
            $admins->add_cap( 'edit_published_jscp_players' );  
            $admins->add_cap( 'delete_published_jscp_players' ); 
            $admins->add_cap( 'delete_others_jscp_players' ); 
            $admins->add_cap( 'publish_jscp_player' ); 
            $admins->add_cap( 'read_jscp_player' ); 
            $admins->add_cap( 'delete_jscp_player' );

            $admins->add_cap( 'edit_jscp_team' );
            $admins->add_cap( 'edit_jscp_teams' );
            $admins->add_cap( 'edit_others_jscp_team' );
            $admins->add_cap( 'edit_published_jscp_teams' );
            $admins->add_cap( 'delete_published_jscp_teams' );
            $admins->add_cap( 'delete_others_jscp_teams' );
            $admins->add_cap( 'publish_jscp_teams' );
            $admins->add_cap( 'read_jscp_team' );
            $admins->add_cap( 'delete_jscp_team' );


            $admins->add_cap( 'edit_jscp_match' ); 
            $admins->add_cap( 'edit_jscp_matchs' ); 
            $admins->add_cap( 'edit_others_jscp_match' ); 
            $admins->add_cap( 'edit_published_jscp_matchs' );  
            $admins->add_cap( 'delete_published_jscp_matchs' ); 
            $admins->add_cap( 'delete_others_jscp_matchs' );
            $admins->add_cap( 'publish_jscp_match' ); 
            $admins->add_cap( 'read_jscp_match' ); 
            $admins->add_cap( 'delete_jscp_match' ); 
        }
        

    }
    public static function addCapToCurrentUser($cap){
        $user_id = get_current_user_id();
        if($user_id){
            $user = new WP_User( $user_id );
            $user->add_cap( $cap );
        }
    }
    public static function removeCapToCurrentUser($cap){
        $user_id = get_current_user_id();
        if($user_id){
            $user = new WP_User( $user_id );
            $user->remove_cap( $cap );
        }
    }
    
    public static function alter_postlist_query($query) {
	    //gets the global query var object
	    global $pagenow;
        if(!function_exists('wp_get_current_user')) {
            include(ABSPATH . "wp-includes/pluggable.php");
        }
        //query for player list
        if(!current_user_can('manage_options')){
            if($pagenow == 'edit.php' && $query->query['post_type'] == 'joomsport_player'){
                $query->set('author' ,get_current_user_id());
            }
            if($pagenow == 'edit.php' && $query->query['post_type'] == 'joomsport_team'){
                $query->set('author' ,false);

                $meta_query = $query->get('meta_query');
                if(!is_array($meta_query)){$meta_query = array();}


                $meta_query[] = array(
                    'key'=>'_joomsport_team_moderator',
                    'value'=>get_current_user_id(),
                    'compare'=>'=',
                );
                $query->set('meta_query',$meta_query);


            }
        }

        //we remove the actions hooked on the '__after_loop' (post navigation)
        remove_all_actions ( '__after_loop');
    }
    
    public static function getTeamsArray($t_single = 0){
        $lists = array();
        if(!function_exists('wp_get_current_user')) {
            include(ABSPATH . "wp-includes/pluggable.php");
        }
        if(!current_user_can('manage_options')){

            $teams = get_posts(array(
                'post_type' => $t_single?'joomsport_player':'joomsport_team',
                'post_status'      => 'publish',
                'posts_per_page'   => -1,
                'author' => get_current_user_id()
                )
            );

            for($intA=0;$intA<count($teams);$intA++){
                
                $lists[] = $teams[$intA]->ID;
            }
        }
        return $lists;
    }
    
    public static function canAddMatch($season_id, $home, $away){
        if(!function_exists('wp_get_current_user')) {
            include(ABSPATH . "wp-includes/pluggable.php");
        }
        if(!current_user_can('manage_options')){
            if(!current_user_can('edit_jscp_matchs')){
                return false;
            }

            $posts = self::getUserPosts();

            //if($homeObj->post_author == get_current_user_id() || $awayObj->post_author == get_current_user_id()){
            if(is_array($posts) && (in_array($home, $posts) || in_array($away, $posts))){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
    public static function canAddTeam(){
        if(!function_exists('wp_get_current_user')) {
            include(ABSPATH . "wp-includes/pluggable.php");
        }
        if(!current_user_can('manage_options')){
            
            $teams = new WP_Query(array(
                'post_type' => 'joomsport_team',
                'posts_per_page'   => -1,
                'post_status' => 'publish',
                'author' => get_current_user_id()   
            ));
            $your_teams = $teams->post_count;
            if(JoomsportSettings::get('teams_per_account',0) && JoomsportSettings::get('teams_per_account',0) <= $your_teams){
                return false;
            }
        }
        return true;
    }
    public static function canAddPlayer(){
        if(!function_exists('wp_get_current_user')) {
            include(ABSPATH . "wp-includes/pluggable.php");
        }
        if(!current_user_can('manage_options')){
            
            $players = new WP_Query(array(
                'post_type' => 'joomsport_player',
                'posts_per_page'   => -1,
                'post_status' => 'publish',
                'author' => get_current_user_id()   
            ));
            $your_players = $players->post_count;
            if(JoomsportSettings::get('player_per_account',0) && JoomsportSettings::get('player_per_account',0) <= $your_players){
                return false;
            }
        }
        return true;
    }
    public static function getUserPosts(){
        $players = new WP_Query(array(
            'post_type' => 'joomsport_player',
            'posts_per_page'   => -1,
            'post_status' => 'publish',
            'author' => get_current_user_id()
        ));
        $teams = new WP_Query(array(
            'post_type' => 'joomsport_team',
            'posts_per_page'   => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key'=>'_joomsport_team_moderator',
                    'value'=>get_current_user_id(),
                    'compare'=>'=',
                )
            )
        ));

        $posts = array();

        for($intA=0; $intA<count($players->posts);$intA++){
            $posts[] = $players->posts[$intA]->ID;
        }
        for($intA=0; $intA<count($teams->posts);$intA++){
            $posts[] = $teams->posts[$intA]->ID;
        }
        return $posts;
    }
    public static function canAddMatches(){

        if(!function_exists('wp_get_current_user')) {
            include(ABSPATH . "wp-includes/pluggable.php");
        }
        if(!current_user_can('manage_options')){
            if(!JoomsportSettings::get('moder_create_matches_reg',0)){
                return false;
            }
        }
        return true;
    }
    
    public static function loadModerCapabilities(){

        $admins = get_role( 'joomsport_moderator' );
        $admins->remove_cap( 'delete_jscp_match' ); 
        $admins->remove_cap('delete_published_jscp_matchs' ); 
        $admins->remove_cap('delete_others_jscp_matchs' );
        $admins->remove_cap( 'edit_others_jscp_match' ); 
        $admins->remove_cap( 'edit_others_jscp_team' );
        $admins->remove_cap( 'edit_others_jscp_player' ); 
        if(!current_user_can('manage_options') && (!JoomsportSettings::get('moder_create_matches_reg', 0) && !JoomsportSettings::get('moder_edit_matches_reg', 0))){
            $admins->remove_cap( 'jsp_matchday_manage');
            $admins->remove_cap( 'jsp_matchday_edit');
            $admins->remove_cap( 'edit_jscp_match' ); 
            $admins->remove_cap( 'edit_jscp_matchs' );
            
            $admins->remove_cap( 'edit_published_jscp_matchs' );  
            $admins->remove_cap( 'publish_jscp_match' ); 
            $admins->remove_cap( 'read_jscp_match' ); 
            

        }elseif(!current_user_can('manage_options')){
            $admins->add_cap( 'jsp_matchday_manage');
            $admins->add_cap( 'jsp_matchday_edit');
            $admins->add_cap( 'edit_jscp_match' ); 
            $admins->add_cap( 'edit_jscp_matchs' );
            //$admins->add_cap( 'edit_others_jscp_match' ); 
            $admins->add_cap( 'edit_published_jscp_matchs' );  
            $admins->add_cap( 'publish_jscp_match' ); 
            $admins->add_cap( 'read_jscp_match' ); 
            if(!JoomsportSettings::get('moder_edit_matches_reg', 0)){
                //$admins->remove_cap( 'edit_jscp_matchs' );
                $admins->remove_cap( 'edit_jscp_match' );
                $admins->remove_cap( 'edit_published_jscp_matchs' );  
            }

        } 

    }
    
    public static function isAdmin(){
        if(!function_exists('wp_get_current_user')) {
            include(ABSPATH . "wp-includes/pluggable.php");
        }
        if(!current_user_can('manage_options')){
            return false;
        }else{
            return true;
        }
    }
    
    public static function canJoinSeasons($team_id){
        $results = array();
        $args = array(
            'posts_per_page' => -1,
            'offset'           => 0,
            'orderby'          => 'title',
            'order'            => 'ASC',
            'post_type'        => 'joomsport_season',
            'post_status'      => 'publish',
            
        );


        $args['meta_query'] = array(
            array(
               array(
                    'key'     => '_joomsport_season_complex',
                    'value'   => '1',
                    'compare' => 'NOT EXISTS',
                    )
            )
         );

        $posts_array = get_posts( $args );
        for($intA=0;$intA<count($posts_array);$intA++){
            $metadata = get_post_meta($posts_array[$intA]->ID,'_joomsport_season_sreg',true);
            $enblreg = (isset($metadata['s_reg']) && $metadata['s_reg'])?true:false;
            $s_reg_to = (isset($metadata['s_reg_to']) && $metadata['s_reg_to'])?1:0;
            $s_participant = (isset($metadata['s_participant']) && $metadata['s_participant'])?intval($metadata['s_participant']):0;
            $reg_start = (isset($metadata['reg_start']) && $metadata['reg_start'])?$metadata['reg_start']:'';
            $reg_end = (isset($metadata['reg_end']) && $metadata['reg_end'])?$metadata['reg_end']:'';
            
            $canreg = true;
            
            if($enblreg){
                if($s_reg_to && $s_participant){
                    //get seasons partic
                    $partic = JoomSportHelperObjects::getParticipiants($posts_array[$intA]->ID);
                    if(count($partic) >= $s_participant){
                        $canreg = false;
                    }
                }
                $part_list = get_post_meta($posts_array[$intA]->ID,'_joomsport_season_participiants',true);
                if(is_array($part_list) && in_array($team_id, $part_list)){
                    $canreg = false;
                }
                if($reg_start && $reg_start > gmdate("Y-m-d")){
                    $canreg = false;
                }
                if($reg_end && $reg_end < gmdate("Y-m-d")){
                    $canreg = false;
                }
                if($canreg){
                    $term_list = wp_get_post_terms($posts_array[$intA]->ID, 'joomsport_tournament', array("fields" => "all"));
                    if(count($term_list)){
                        //$term_meta = get_option( "taxonomy_".$term_list[0]->term_id."_metas");
                        $term_meta = JoomsportTermsMeta::getTermMeta($term_list[0]->term_id);
                        if($term_meta['t_single'] == '0'){
                            $std = new stdClass();
                            $std->name = esc_attr($posts_array[$intA]->post_title);
                            $std->id = $posts_array[$intA]->ID;
                            if(!isset($results[$term_list[0]->name])){
                                $results[esc_attr($term_list[0]->name)] = array();
                            }
                            array_push($results[esc_attr($term_list[0]->name)], $std);
                        }
                    }
                }
            }
        }
        
        return $results;
    }

    public static function get_users_by_role($role, $orderby, $order) {
        $args = array(
            'role'    => $role,
            'orderby' => $orderby,
            'order'   => $order
        );

        $users = get_users( $args );

        return $users;
    }

    public static function setModeratorMeta(){
        $moderators = self::get_users_by_role('joomsport_moderator', 'user_nicename', 'ASC');
        $moderArray = array();
        for($intA=0;$intA<count($moderators);$intA++){
            $moderArray[] = $moderators[$intA]->id;
        }
        if(count($moderArray)){
            $teams = new WP_Query(array(
                'post_type' => 'joomsport_team',
                'posts_per_page'   => -1,
                'post_status' => 'publish',
                'author__in' => $moderArray

            ));

            for($intA=0; $intA<count($teams->posts);$intA++){
                $postID = $teams->posts[$intA]->ID;
                add_post_meta($postID, "_joomsport_team_moderator", $teams->posts[$intA]->post_author);
            }
        }
    }
    
}


add_action('pre_get_posts',array('JoomSportUserRights','alter_postlist_query'));


function joomsport_moderator_notes() {
    global $post;
    if(!empty($post)){
        switch ($post->post_type) {
            case 'joomsport_team':
                if(!JoomSportUserRights::canAddTeam()){
                    ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php echo esc_html__( 'Teams number limit is reached', 'joomsport-sports-league-results-management' ); ?></p>
                    </div>
                    <?php
                }

                break;
            case 'joomsport_player':
                if(!JoomSportUserRights::canAddPlayer()){
                    ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php echo esc_html__( 'Players number limit is reached', 'joomsport-sports-league-results-management' ); ?></p>
                    </div>
                    <?php
                }

                break;
            default:
                break;
        }
    }

}
add_action( 'admin_notices', 'joomsport_moderator_notes' );
add_action("load-post-new.php", 'joomsport_moderator_check');

function joomsport_moderator_check()
{
    $post_type = isset($_GET["post_type"])?sanitize_text_field(wp_unslash($_GET["post_type"])):'';
    switch ($post_type) {
        case 'joomsport_team':
            if(!JoomSportUserRights::canAddTeam()){
                wp_redirect("edit.php?post_type=joomsport_team");
            }
            break;
        case 'joomsport_player':
            if(!JoomSportUserRights::canAddPlayer()){
                wp_redirect("edit.php?post_type=joomsport_player");
            }
                break;
        default:
            break;
    }
 
}


add_filter('user_has_cap', 'jsport_moderator_can_edit_published_posts', 100, 4);
function jsport_moderator_can_edit_published_posts($allcaps, $caps, $args, $user) {
    global $post;
    // Do we have a post?
    if ( ! $post ) {
        return $allcaps;
    }
    if($post->post_type != 'joomsport_team'){
        return $allcaps;
    }
    if(!$user->ID){
        return $allcaps;
    }


    // Is the user a contributor?
    if ( ! isset( $allcaps['joomsport_moderator'] ) || true !== $allcaps['joomsport_moderator'] ) {
        return $allcaps;
    }
    // Is the user the author of the post
    if ( $post->post_author == $user->ID ) {
        return $allcaps;
    }
    $moderators = get_post_meta($post->ID,'_joomsport_team_moderator');
    //var_dump($moderators);die();
    if(is_array($moderators) && in_array($user->ID,$moderators)){
        $allcaps["edit_others_jscp_team"] = true;
        $allcaps['edit_published_posts'] = true;
    }

    return $allcaps;
}
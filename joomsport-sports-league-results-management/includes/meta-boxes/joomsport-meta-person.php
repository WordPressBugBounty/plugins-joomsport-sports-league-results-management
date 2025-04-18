<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class JoomSportMetaPerson {
    public static function output( $post ) {
        global $post, $thepostid, $wp_meta_boxes;
        
        
        $thepostid = $post->ID;

        wp_nonce_field( 'joomsport_person_savemetaboxes', 'joomsport_person_nonce' );
        ?>
        <div id="joomsportContainerBE">
            
            <div>
                <div>
                    <div>
                        <?php
                        do_meta_boxes(get_current_screen(), 'joomsportintab_person1', $post);
                        unset($wp_meta_boxes[get_post_type($post)]['joomsportintab_person1']);
                        ?>

                    </div>    
                </div>
            </div>   
            
        </div>
        <?php
    }
        
        
    public static function js_meta_personal($post){

        $metadata = get_post_meta($post->ID,'_joomsport_person_personal',true);

        ?>
        <div class="jsminwdhtd jstable">
            <div class="jstable-row">
                <div class="jstable-cell">
                    <?php echo esc_html__('First name', 'joomsport-sports-league-results-management');?>
                </div>
                <div class="jstable-cell">
                    <input type="text" name="personal[first_name]" value="<?php echo isset($metadata['first_name'])?esc_attr($metadata['first_name']):""?>" />
                </div>
            </div>
            <div class="jstable-row">
                <div class="jstable-cell">
                    <?php echo esc_html__('Last name', 'joomsport-sports-league-results-management');?>
                </div>
                <div class="jstable-cell">
                    <input type="text" name="personal[last_name]" value="<?php echo isset($metadata['last_name'])?esc_attr($metadata['last_name']):""?>" />
                </div>
            </div>
        </div>
        <?php
    }
    public static function js_meta_about($post){

        $metadata = get_post_meta($post->ID,'_joomsport_person_about',true);
        wp_editor($metadata, 'about',array("textarea_rows"=>3));


    }

    public static function js_meta_ef($post){

        $metadata = get_post_meta($post->ID,'_joomsport_person_ef',true);
        
        $efields = JoomSportHelperEF::getEFList('6', 0);

        if(count($efields)){
            echo '<div class="jsminwdhtd jstable">';
            foreach ($efields as $ef) {

                JoomSportHelperEF::getEFInput($ef, isset($metadata[$ef->id])?$metadata[$ef->id]:null);
                ?>
                
                <div class="jstable-row">
                    <div class="jstable-cell"><?php echo esc_html($ef->name)?></div>
                    <div class="jstable-cell">
                        <?php 
                        if($ef->field_type == '2'){
                            wp_editor(isset($metadata[$ef->id])?$metadata[$ef->id]:'', 'ef_'.esc_attr($ef->id),array("textarea_rows"=>3));
                            echo '<input type="hidden" name="ef['.esc_attr($ef->id).']" value="ef_'.esc_attr($ef->id).'" />';
                        }else{
                            echo wp_kses($ef->edit,JoomsportSettings::getKsesEFEdit());
                        }
                        ?>
                    </div>    
                        
                </div>    
                <?php
            }
            echo '</div>';
        }else{
            $link = get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-page-extrafields');
             printf( esc_html__( 'There are no extra fields assigned to this section. Create new one on %s Extra fields list %s', 'joomsport-sports-league-results-management' ), '<a href="'.esc_url($link).'">','</a>' );

        }
        
    }

    
    public static function joomsport_person_save_metabox($post_id, $post){
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['joomsport_person_nonce'] ) ? sanitize_text_field(wp_unslash($_POST['joomsport_person_nonce'])) : '';
        $nonce_action = 'joomsport_person_savemetaboxes';
 
        // Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }
 
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
 
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
 
        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
 
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }
        
        if('joomsport_person' == isset($_POST['post_type'])?sanitize_text_field(wp_unslash($_POST['post_type'])):'' ){
            self::saveMetaPersonal($post_id);
            self::saveMetaAbout($post_id);
            self::saveMetaEF($post_id);
            
        }
    }
    
    private static function saveMetaPersonal($post_id){
        $nonce_name   = isset( $_POST['joomsport_person_nonce'] ) ? sanitize_text_field(wp_unslash($_POST['joomsport_person_nonce'])) : '';
        $nonce_action = 'joomsport_person_savemetaboxes';

        // Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }

        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
        $meta_array = array_map( 'sanitize_text_field', isset($_POST['personal'])?wp_unslash( $_POST['personal'] ):array() );
        update_post_meta($post_id, '_joomsport_person_personal', $meta_array);
    }
    private static function saveMetaAbout($post_id){
        $nonce_name   = isset( $_POST['joomsport_person_nonce'] ) ? sanitize_text_field(wp_unslash($_POST['joomsport_person_nonce'])) : '';
        $nonce_action = 'joomsport_person_savemetaboxes';

        // Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }

        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
        $meta_data = isset($_POST['about'])?  wp_kses_post(wp_unslash($_POST['about'])):'';
        update_post_meta($post_id, '_joomsport_person_about', $meta_data);
    }
    private static function saveMetaEF($post_id){
        $nonce_name   = isset( $_POST['joomsport_person_nonce'] ) ? sanitize_text_field(wp_unslash($_POST['joomsport_person_nonce'])) : '';
        $nonce_action = 'joomsport_person_savemetaboxes';

        // Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }

        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
        $meta_array = array();
        $ef = isset($_POST['ef'])?array_map("sanitize_text_field", wp_unslash($_POST['ef'])):array();
        if($ef && count($ef)){
            foreach ($ef as $key => $value){
                if(isset($_POST['ef_'.$key])){
                    $meta_array[$key] = wp_kses_post(wp_unslash($_POST['ef_'.$key]));
                }else{
                    $meta_array[$key] = sanitize_text_field($value);
                }
            }
        }
        update_post_meta($post_id, '_joomsport_person_ef', $meta_array);
    }
    
}

<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

require_once JOOMSPORT_PATH_INCLUDES . 'meta-boxes' . DIRECTORY_SEPARATOR . 'joomsport-meta-team.php';

class JoomSportPostTeam {
    public function __construct() {

    }
    public static function init(){
        self::register_post_types();
    }
    public static function register_post_types(){
        add_action("admin_init", array("JoomSportPostTeam","admin_init"));
        add_action( 'edit_form_after_title',  array( 'JoomSportPostTeam','team_edit_form_after_title') );
        add_action( 'admin_footer', array("JoomSportPostTeam",'joomsport_team_action_javascript') );
        add_action( 'wp_ajax_team_seasonrelated', array("JoomSportPostTeam",'joomsport_team_seasonrelated') );
        add_filter( 'post_type_labels_joomsport_team', array( 'JoomSportPostTeam','team_replace_default_featured_image_meta_box'), 10,1 );
        $slug = get_option( 'joomsportslug_joomsport_team', null );
        register_post_type( 'joomsport_team',
                apply_filters( 'joomsport_register_post_type_team',
                        array(
                                'labels'              => array(
                                                'name'               => __( 'Team', 'joomsport-sports-league-results-management' ),
                                                'singular_name'      => __( 'Team', 'joomsport-sports-league-results-management' ),
                                                'menu_name'          => _x( 'Teams', 'Admin menu name Teams', 'joomsport-sports-league-results-management' ),
                                                'add_new'            => __( 'Add Team', 'joomsport-sports-league-results-management' ),
                                                'add_new_item'       => __( 'Add New Team', 'joomsport-sports-league-results-management' ),
                                                'edit'               => __( 'Edit', 'joomsport-sports-league-results-management' ),
                                                'edit_item'          => __( 'Edit Team', 'joomsport-sports-league-results-management' ),
                                                'new_item'           => __( 'New Team', 'joomsport-sports-league-results-management' ),
                                                'view'               => __( 'View Team', 'joomsport-sports-league-results-management' ),
                                                'view_item'          => __( 'View Team', 'joomsport-sports-league-results-management' ),
                                                'search_items'       => __( 'Search Team', 'joomsport-sports-league-results-management' ),
                                                'not_found'          => __( 'No Team found', 'joomsport-sports-league-results-management' ),
                                                'not_found_in_trash' => __( 'No Team found in trash', 'joomsport-sports-league-results-management' ),
                                                'parent'             => __( 'Parent Team', 'joomsport-sports-league-results-management' )
                                        ),
                                'description'         => __( 'This is where you can add new team.', 'joomsport-sports-league-results-management' ),
                                'public'              => true,
                                'show_ui'             => true,
                                'show_in_menu'        => (current_user_can('manage_options')?'joomsport':null),
                                'publicly_queryable'  => true,
                                'exclude_from_search' => false,
                                'hierarchical'        => false,
                                'query_var'           => true,
                                'supports'            => array( 'title','thumbnail' ),
                                'show_in_nav_menus'   => true,
                                'capability_type' => 'jscp_team',
                                'capabilities' => array(
                                    'edit_post' => 'edit_jscp_team',
                                    'edit_posts' => 'edit_jscp_teams',
                                    'edit_others_posts' => 'edit_others_jscp_team',

                                    'publish_posts' => 'publish_jscp_teams',
                                    'read_post' => 'read_jscp_team',
                                    'delete_post' => 'delete_jscp_team',
                                    'delete_posts' => 'delete_jscp_teams',
                                    'delete_others_posts' => 'delete_others_jscp_teams'
                                ),
                                'map_meta_cap' => true,
                                'rewrite' => array(
                                    'slug' => $slug?$slug:'joomsport_team'
                                )
                        )
                )
        );
    }
    public static function team_edit_form_after_title($post_type){
        global $post, $wp_meta_boxes;

        if($post_type->post_type == 'joomsport_team'){
            
            JoomSportMetaTeam::output($post_type);

        }
    

    }
    public static function team_replace_default_featured_image_meta_box($labels ) {

	$labels->featured_image 	= __('Logo', 'joomsport-sports-league-results-management');
	$labels->set_featured_image 	= __('Set logo', 'joomsport-sports-league-results-management');
	$labels->remove_featured_image 	= __('Remove logo', 'joomsport-sports-league-results-management');
	$labels->use_featured_image 	= __('Use as logo', 'joomsport-sports-league-results-management');

	return $labels;

    } 
    public static function admin_init(){
        add_meta_box('joomsport_team_personal_form_meta_box', __('Personal', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_personal'), 'joomsport_team', 'joomsportintab_team1', 'default');
        add_meta_box('joomsport_team_about_form_meta_box', __('About team', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_about'), 'joomsport_team', 'joomsportintab_team1', 'default');
        add_meta_box('joomsport_team_seasons_form_meta_box', __('Assign to season', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_seasons'), 'joomsport_team', 'joomsportintab_team1', 'default');
        add_meta_box('joomsport_team_ef_form_meta_box', __('Extra fields', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_ef'), 'joomsport_team', 'joomsportintab_team1', 'default');
        add_meta_box('joomsport_team_players_form_meta_box', __('Players', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_players'), 'joomsport_team', 'joomsportintab_team2', 'default');
        add_meta_box('joomsport_team_bonuses_form_meta_box', __('Bonuses', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_bonuses'), 'joomsport_team', 'joomsportintab_team2', 'default');

        add_meta_box('joomsport_team_ef_assigned_form_meta_box', __('Extra fields assigned to the season', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_ef_assigned'), 'joomsport_team', 'joomsportintab_team2', 'default');
        add_meta_box('joomsport_team_icons_assigned_meta_box', __('Team icons assigned to the season', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_icons_assigned'), 'joomsport_team', 'joomsportintab_team2', 'default');

        add_meta_box('joomsport_team_venue_form_meta_box', __('Home venue', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_venue'), 'joomsport_team', 'side', 'default');
        if(JoomSportUserRights::isAdmin() && JoomsportSettings::get('team_reg') == 1) {
            add_meta_box('joomsport_team_moderator_form_meta_box', __('Team Moderator', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam', 'js_meta_moderator'), 'joomsport_team', 'side', 'default');
        }

        add_action( 'save_post',      array( 'JoomSportMetaTeam', 'joomsport_team_save_metabox' ), 10, 2 );
    }
    public static function joomsport_team_action_javascript(){
        ?>
        <script type="text/javascript" >
	jQuery(document).ready(function($) {
            jQuery('select[name="stb_season_id"]').on("change",function(){
                var data = {
			'action': 'team_seasonrelated',
			'season_id': jQuery('select[name="stb_season_id"]').val(),
            'post_id':jQuery('#post_ID').val(),
            'security': jslrmObj.jnonce,
		};

		jQuery.post(ajaxurl, data, function(response) {
            var txt = document.createElement('textarea');
            txt.innerHTML = response;
            response =  txt.value;
                    var res = jQuery.parseJSON( response );
                    if(res.players){
                        jQuery('#js_team_playersDIV').html(res.players);
                        jQuery("#stb_players_id").chosen({disable_search_threshold: 10,width: "95%",disable_search:false});
                    }
                    if(res.bonuses){
                        jQuery('#js_team_bonusesDIV').html(res.bonuses);
                    }
                    if(res.efassigned){
                        jQuery('#js_team_efassignedDIV').html(res.efassigned);
                    }
                    if(res.iconsassigned){
                        jQuery('#js_team_iconsassignedDIV').html(res.iconsassigned);
                    }
                    //jQuery('#stb_players_id').trigger('liszt:updated');
                    
		});
            });
		
	});
	</script>
        <?php
    }
    public static function joomsport_team_seasonrelated(){
        global $wpdb;
        if (!current_user_can('manage_options')) { wp_die('-1', 403); }
        check_ajax_referer("joomsportajaxnonce", "security");
        
        $season_id = isset($_POST['season_id'])?intval($_POST['season_id']):0;
        $post_id = isset($_POST['post_id'])?intval($_POST['post_id']):0;
        $result = array(
            "players"=>__('No season selected','joomsport-sports-league-results-management'),
            "bonuses"=>__('No season selected','joomsport-sports-league-results-management'),
            "efassigned"=>__('No season selected','joomsport-sports-league-results-management'),
            "iconsassigned"=>__('No season selected','joomsport-sports-league-results-management'),
            );
        if($season_id && $post_id){
            $playersin = get_post_meta($post_id,'_joomsport_team_players_'.$season_id,true);
            $args = array(
                'posts_per_page' => -1,
                'offset'           => 0,
                'orderby'          => 'title',
                'order'            => 'ASC',
                'post_type'        => 'joomsport_player',
                'post_status'      => 'publish',
            );
            if(!JoomSportUserRights::isAdmin()){
                $args["author"] = get_current_user_id();
            }
            $posts_players = get_posts( $args );

            if(count($posts_players)){
                $result['players'] = '<select name="players_id[]" id="stb_players_id" data-placeholder="'.esc_attr(__('Add item','joomsport-sports-league-results-management')).'" class="jswf-chosen-select" multiple>';
                foreach ($posts_players as $tm) {
                    $selected = '';
                    if(is_array($playersin) && in_array($tm->ID, $playersin)){
                        $selected = ' selected';
                    }
                    $result['players'] .= '<option value="'.esc_attr($tm->ID).'" '.$selected.'>'.$tm->post_title.'</option>';


                }
                $result['players'] .= '</select>';

            }
            $bonus = get_post_meta($post_id,'_joomsport_team_bonuses_'.$season_id,true);
            $result['bonuses'] = '<table class="jsminwdhtd"><tr><td>'.__('Bonus points','joomsport-sports-league-results-management').'</td><td><input type="text" name="js_bonuses" value="'.esc_attr($bonus).'"></td></tr></table>';
            
            $metadata = get_post_meta($post_id,'_joomsport_team_ef_'.$season_id,true);

            $efields = JoomSportHelperEF::getEFList('1', 1, 1);

            if(count($efields)){
                $html_ef = '<div class="jsminwdhtd jstable">';
                foreach ($efields as $ef) {

                    JoomSportHelperEF::getEFInput($ef, isset($metadata[$ef->id])?$metadata[$ef->id]:null,'efs');
                    //var_dump($ef);
                    
                    $html_ef .= '<div class="jstable-row">';
                        $html_ef .= '<div class="jstable-cell">'.$ef->name.'</div>';
                        $html_ef .= '<div class="jstable-cell">';

                            if($ef->field_type == '2'){
                                ob_start();
                                wp_editor(isset($metadata[$ef->id])?$metadata[$ef->id]:'', 'efs_'.$ef->id,array("textarea_rows"=>3));
                                //$html_ef .= '<textarea id="efs_'.$ef->id.'"></textarea>';
                                $html_ef .= ob_get_contents();
                                ob_end_clean();
                                $html_ef .=  '<input type="hidden" name="efs['.esc_attr($ef->id).']" value="efs_'.esc_attr($ef->id).'" />';
                                $html_ef .= '<script>tinymce.execCommand( \'mceAddEditor\', true, jQuery("#efs_'.esc_attr($ef->id).'").attr("id") );quicktags({id : jQuery("#efs_'.esc_attr($ef->id).'").attr("id")});</script>';
                            }else{
                                $html_ef .=  $ef->edit;
                            }
                           
                        $html_ef .= '</div>';    

                    $html_ef .= '</div>';

                }
                $html_ef .= '</div>';
                $result['efassigned'] = $html_ef;
            }else{
                $result['efassigned'] = 'There are no extra fields assigned to this section.';
            }

            $icons = $wpdb->get_results("SELECT * FROM ".$wpdb->joomsport_icons." ORDER BY ordering,iconName");
            if($icons){
                $iconsAssigned = $wpdb->get_results("SELECT i.* FROM ".$wpdb->joomsport_icons_related." as ir JOIN ".$wpdb->joomsport_icons." as i ON i.id=ir.iconID  WHERE ir.participantID={$post_id} AND ir.seasonID={$season_id} ORDER BY ir.ordering,ir.id");
                $html = '<div class="controls">';
                $html .= '<select id="itemSelectIcons">';
                foreach ($icons as $icon){
                    $html .= '<option value="'.$icon->id.'">'.$icon->iconName.'</option>';
                }
                $html .= '</select>';
                $html .= '<input type="button" id="addBtnIcons" value="Add" />';
                $html .= '</div>';

                $html .= '<script>let items = [];let nextId = 1;</script>';

                $html .= '<ul id="listIcons">';
                if($iconsAssigned){
                    $intP = 1;
                    foreach($iconsAssigned as $iA){
                        $html .= '<script>items.push({ text: \''.$iA->iconName.'\', id: nextId++, value: '.$iA->id.' });</script>';
                        //$html .= '<li draggable="true" data-id="'.$intP.'"><span class="handle">⋮⋮</span><span class="text">'.$iA->iconName.'</span><input type="hidden" name="vIcons[]" value="'.$iA->id.'"><button title="Delete">✕</button></li>';
                        $intP++;
                    }
                }

                $html .= '</ul> ';
                $html .= '<script>


  const selectEl = document.getElementById(\'itemSelectIcons\');
  const addBtn   = document.getElementById(\'addBtnIcons\');
  const clearBtn = document.getElementById(\'clearBtn\');
  const listEl   = document.getElementById(\'listIcons\');



  function addSelected() {
    const value = selectEl.value;
const opt = selectEl.selectedOptions[0];
const label = opt.textContent.trim();

    if (!value) return;


    const exists = items.some(
      it => it.value === value
    );
    if (exists) return;

    items.push({ text: label, id: nextId++, value: value });
    render();
  }


  function removeItem(id) {
    items = items.filter(it => it.id !== id);
    render();
  }


  function render() {
    listEl.innerHTML = \'\';

    if (items.length === 0) {
      listEl.innerHTML = \'<li class="empty">Empty</li>\';
    } else {
      items.forEach(item => {
        const li = document.createElement(\'li\');
        li.draggable = true;
        li.dataset.id = item.id;

        const handle = document.createElement(\'span\');
        handle.className = \'handle\';
        handle.textContent = \'⋮⋮\';

        const span = document.createElement(\'span\');
        span.className = \'text\';
        span.textContent = item.text;
        
        const hidden = document.createElement(\'input\');
          hidden.type = \'hidden\';
          hidden.name = \'vIcons[]\';
          hidden.value = item.value;

        const btn = document.createElement(\'button\');
        btn.textContent = \'✕\';
        btn.title = \'Delete\';
        btn.addEventListener(\'click\', e => {
          e.stopPropagation();
          removeItem(item.id);
        });

        li.appendChild(handle);
        li.appendChild(span);
        li.appendChild(hidden);
        li.appendChild(btn);
        listEl.appendChild(li);
      });
    }


  }


  let draggedEl = null;

  listEl.addEventListener(\'dragstart\', e => {
    const li = e.target.closest(\'li\');
    if (!li || li.classList.contains(\'empty\')) return;

    draggedEl = li;
    li.classList.add(\'dragging\');
    e.dataTransfer.effectAllowed = \'move\';
    e.dataTransfer.setData(\'text/plain\', li.dataset.id);
  });

  listEl.addEventListener(\'dragend\', () => {
    if (draggedEl) draggedEl.classList.remove(\'dragging\');
    document.querySelectorAll(\'li.drag-over\')
      .forEach(el => el.classList.remove(\'drag-over\'));
    draggedEl = null;
  });

  listEl.addEventListener(\'dragover\', e => {
    e.preventDefault();
    e.dataTransfer.dropEffect = \'move\';

    const target = e.target.closest(\'li\');
    if (!target || target === draggedEl || target.classList.contains(\'empty\')) return;

    document.querySelectorAll(\'li.drag-over\')
      .forEach(el => el.classList.remove(\'drag-over\'));
    target.classList.add(\'drag-over\');

    const rect = target.getBoundingClientRect();
    const after = (e.clientY - rect.top) > rect.height / 2;
    const parent = target.parentNode;

    if (after) {
      parent.insertBefore(draggedEl, target.nextSibling);
    } else {
      parent.insertBefore(draggedEl, target);
    }
  });

  listEl.addEventListener(\'drop\', e => {
    e.preventDefault();


    const newOrder = [...listEl.querySelectorAll(\'li\')]
      .filter(li => !li.classList.contains(\'empty\'))
      .map(li => Number(li.dataset.id));

    items.sort((a, b) => newOrder.indexOf(a.id) - newOrder.indexOf(b.id));
  });


  addBtn.addEventListener(\'click\', addSelected);


  render();
</script>';
                $result['iconsassigned'] = $html;
            }else{
                $result['iconsassigned'] = 'There are no icons.';
            }

        }
        
        
        echo (wp_json_encode($result));
        
        wp_die();
    }
}    

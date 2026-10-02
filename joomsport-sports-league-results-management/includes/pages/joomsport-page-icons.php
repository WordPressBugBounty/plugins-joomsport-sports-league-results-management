<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class JoomSportIcons_List_Table extends WP_List_Table {

    public function __construct() {

        parent::__construct( array(
                'singular' => __( 'Icon', 'joomsport-sports-league-results-management' ),
                'plural'   => __( 'Icons', 'joomsport-sports-league-results-management' ),
                'ajax'     => false 

        ) );
        /** Process bulk action */
        $this->process_bulk_action();

    }
    public static function get_icons( $per_page = 5, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->joomsport_icons}";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
          $sql .= ' ORDER BY ' . sanitize_sql_orderby( "{$_REQUEST['orderby']} {$_REQUEST['order']}" );

        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }
    public static function delete_icon( $id ) {
        global $wpdb;

        $wpdb->delete(
          "{$wpdb->joomsport_icons}",
          array( 'id' => $id ),
          array( '%d' )
        );
    }
    public static function record_count() {
        global $wpdb;

        return $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->joomsport_icons}" );
    }
    public function no_items() {
        echo esc_html__( 'No icons available.', 'joomsport-sports-league-results-management' );
    }
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'joomsport_delete_icons' );

        $title = '<strong><a href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-icons-form&id='.absint( $item['id'] )).'">' . $item['iconName'] . '</a></strong>';

        $actions = array(
          'delete' => sprintf( '<a href="?page=%s&action=%s&icon=%s&_wpnonce=%s">Delete</a>', (isset($_GET['page'])?( esc_attr(wp_unslash($_GET['page'])) ):''), 'delete', absint( $item['id'] ), $delete_nonce )
        );

        return $title . $this->row_actions( $actions );
    }
    
    function column_cb( $item ) {
        return sprintf(
          '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }
    function get_columns() {
        $columns = array(
          'cb'      => '<input type="checkbox" />',
          'name'    => __( 'Name', 'joomsport-sports-league-results-management' )
        );

        return $columns;
    }
    public function get_sortable_columns() {
        $sortable_columns = array(
          'name' => array( 'name', true )
        );

        return $sortable_columns;
    }
    public function get_bulk_actions() {
        $actions = array(
          'bulk-delete' => 'Delete'
        );

        return $actions;
    }
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        

        $per_page     = $this->get_items_per_page( 'icons_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
          'total_items' => $total_items, //WE have to calculate the total number of items
          'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );


        $this->items = self::get_icons( $per_page, $current_page );
    }
    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
          // In our file that handles the request, verify the nonce.
          $nonce = isset($_REQUEST['_wpnonce'])?esc_attr( sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])) ):'';

          if ( ! wp_verify_nonce( $nonce, 'joomsport_delete_icon' ) ) {
            die( 'Error' );
          }
          else {
            self::delete_icon( isset($_GET['icon'])?absint( $_GET['icon'] ):0 );
            wp_redirect( esc_url(get_dashboard_url(). 'admin.php?page=joomsport-page-icons' ) );
            exit;
          }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
             || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = isset( $_POST['bulk-delete'] )?array_map('absint',wp_unslash($_POST['bulk-delete'])):array();

            // loop over the array of record IDs and delete them
          foreach ( $delete_ids as $id ) {
            self::delete_icon( $id );

          }

          wp_redirect( esc_url(get_dashboard_url(). 'admin.php?page=joomsport-page-icons' ) );
          exit;
        }
    }
    
}


class JoomSportIcons_Plugin {

	// class instance
	static $instance;

	// customer WP_List_Table object
	public $customers_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
		//add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}


	public static function set_screen( $status, $option, $value ) {
		return $value;
	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		?>
		<div class="wrap">
			<h2><?php echo esc_html__('Icons', 'joomsport-sports-league-results-management');?>
                        <a class="add-new-h2"
                                 href="<?php echo esc_url(get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-icons-form'));?>"><?php echo esc_html__('Add new', 'joomsport-sports-league-results-management')?></a>
                        </h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->customers_obj->prepare_items();
								$this->customers_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
                    <script type="text/javascript" id="UR_initiator"> (function () { var iid = 'uriid_'+(new Date().getTime())+'_'+Math.floor((Math.random()*100)+1); if (!document._fpu_) document.getElementById('UR_initiator').setAttribute('id', iid); var bsa = document.createElement('script'); bsa.type = 'text/javascript'; bsa.async = true; bsa.src = '//beardev.useresponse.com/sdk/supportCenter.js?initid='+iid+'&wid=6'; (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(bsa); })(); </script>
		</div>
	<?php

	}

	/**
	 * Screen options
	 */
	public function screen_option() {
        $mscr = isset($_POST['wp_screen_options']['option'])?intval($_POST['wp_screen_options']['value']):0;
        if($mscr){
            update_user_meta(get_current_user_id(), 'icons_per_page', $mscr);

        }

		$option = 'per_page';
		$args   = array(
			'label'   => 'Icons',
			'default' => 5,
			'option'  => 'icons_per_page'
		);

		add_screen_option( $option, $args );

		$this->customers_obj = new JoomSportIcons_List_Table();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}


class JoomSportIconsNew_Plugin {
    public static function view(){

        global $wpdb;
        $table_name = $wpdb->joomsport_icons;

        $message = '';
        $notice = '';

        // this is default $item which will be used for new records
        $default = array(
            'id' => 0,
            'iconName' => '',
            'iconImage' => '',
            'ordering' => 0,
            'iconTip' => '',

        );
        $item = array();
        // here we are verifying does this request is post back and have correct nonce
        if (isset($_REQUEST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), basename(__FILE__))) {
            // combine our default item with request params
            $item = shortcode_atts($default, array_map( 'sanitize_text_field', wp_unslash( $_REQUEST )));
            $lists = self::getListValues($item);
            // validate data, and if all ok save item to database
            // if id is zero insert otherwise update
            $item_valid = self::joomsport_icons_validate($item);
            if ($item_valid === true) {
                if ($item['id'] == 0) {
                    $result = $wpdb->insert($table_name, $item);
                    $item['id'] = $wpdb->insert_id;
                    if ($result) {
                        $message = __('Item was successfully saved', 'joomsport-sports-league-results-management');
                    } else {
                        $notice = __('There was an error while saving item', 'joomsport-sports-league-results-management');
                    }
                } else {
                    $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                    if ($result) {
                        $message = __('Item was successfully updated', 'joomsport-sports-league-results-management');
                    } else {
                        $notice = __('There was an error while updating item', 'joomsport-sports-league-results-management');
                    }
                }
                echo '<script> window.location="'.(esc_url(get_dashboard_url())).'admin.php?page=joomsport-page-icons"; </script> ';
                
            } else {
                // if $item_valid not true it contains error message(s)
                $notice = $item_valid;
            }
        }
        else {
            // if this is not post back we load item to edit or give new one to create
            $item = $default;
            if (isset($_REQUEST['id'])) {
                $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->joomsport_icons} WHERE id = %d", intval($_REQUEST['id'])), ARRAY_A);
                if (!$item) {
                    $item = $default;
                    $notice = __('Item not found', 'joomsport-sports-league-results-management');
                }
            }
            $lists = self::getListValues($item);
        }

        // here we adding our custom meta box
        add_meta_box('joomsport_icons_form_meta_box', __('Details', 'joomsport-sports-league-results-management'), array('JoomSportIconsNew_Plugin','joomsport_icon_form_meta_box_handler'), 'joomsport-icons-form', 'normal', 'default');

        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <h2><?php echo esc_html__('Icon', 'joomsport-sports-league-results-management')?> <a class="add-new-h2"
                                        href="<?php echo esc_url(get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-page-icons'));?>"><?php echo esc_html__('back to list', 'joomsport-sports-league-results-management')?></a>
            </h2>

            <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo esc_html($notice) ?></p></div>
            <?php endif;?>
            <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo esc_html($message) ?></p></div>
            <?php endif;?>
            <script>
                jQuery(function($){

                    // Set all variables to be used in scope
                    var frame,
                        metaBox = $('#jseventcontainer'), // Your meta box id here
                        addImgLink = metaBox.find('#jsEventImage'),
                        delImgLink = metaBox.find( '.delete-jsev-img'),
                        imgContainer = metaBox.find( '.jsev-img-container'),
                        imgIdInput = metaBox.find( '.jsev-img-id' );

                    // ADD IMAGE LINK
                    addImgLink.on( 'click', function( event ){

                        event.preventDefault();

                        // If the media frame already exists, reopen it.
                        if ( frame ) {
                            frame.open();
                            return;
                        }

                        // Create a new media frame
                        frame = wp.media({
                            title: 'Select or Upload Media Of Your Chosen Persuasion',
                            button: {
                                text: 'Use this media'
                            },
                            multiple: false  // Set to true to allow multiple files to be selected
                        });


                        // When an image is selected in the media frame...
                        frame.on( 'select', function() {

                            // Get media attachment details from the frame state
                            var attachment = frame.state().get('selection').first().toJSON();

                            // Send the attachment URL to our custom image input field.
                            imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

                            // Send the attachment id to our hidden input
                            imgIdInput.val( attachment.id );

                            // Hide the add image link
                            addImgLink.addClass( 'hidden' );

                            // Unhide the remove image link
                            delImgLink.removeClass( 'hidden' );
                        });

                        // Finally, open the modal on click
                        frame.open();
                    });


                    // DELETE IMAGE LINK
                    delImgLink.on( 'click', function( event ){

                        event.preventDefault();

                        // Clear out the preview image
                        imgContainer.html( '' );

                        // Un-hide the add image link
                        addImgLink.removeClass( 'hidden' );

                        // Hide the delete image link
                        delImgLink.addClass( 'hidden' );

                        // Delete the image id from the hidden input
                        imgIdInput.val( '' );

                    });

                });
            </script>
            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce(basename(__FILE__)))?>"/>
                <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
                <input type="hidden" name="id" value="<?php echo esc_attr($item['id']) ?>"/>

                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content"  class="jsRemoveMB">
                            <?php /* And here we call our custom meta box */ ?>
                            <?php do_meta_boxes('joomsport-icons-form', 'normal', array($item,$lists)); ?>
                            <input type="submit" value="<?php echo esc_attr(__('Save & close', 'joomsport-sports-league-results-management'))?>" id="submit" class="button-primary" name="submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
    public static function joomsport_icon_form_meta_box_handler($item)
    {
        $lists = $item[1];
        $item = $item[0];
    ?>
    <div class="jsrespdiv12">
        <div class="jsBepanel">
            <div class="jsBEheader">
                <?php echo esc_html__('General', 'joomsport-sports-league-results-management'); ?>
            </div>
            <div class="jsBEsettings" id="jseventcontainer">

                <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                    <tbody>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="name"><?php echo esc_html__('Name', 'joomsport-sports-league-results-management')?></label>
                        </th>
                        <td>
                            <input id="iconName" name="iconName" type="text" style="width: 95%" value="<?php echo esc_attr(isset($item['iconName'])?$item['iconName']:"")?>"
                                   size="50" class="code"  required>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="name"><?php echo esc_html__('Tooltip Text', 'joomsport-sports-league-results-management')?></label>
                        </th>
                        <td>
                            <input id="iconTip" name="iconTip" type="text" style="width: 95%" value="<?php echo esc_attr(isset($item['iconTip'])?$item['iconTip']:"")?>"
                                   size="50" class="code"  required>
                        </td>
                    </tr>
                    <tr>
                        <td width="200" valign="middle">
                            <?php echo esc_html__('image', 'joomsport-sports-league-results-management'); ?>
                        </td>
                        <td>
                            <div>
                                <div class="jsev-img-container">
                                    <?php
                                    if($item['iconImage']){
                                        echo wp_get_attachment_image($item['iconImage']);
                                    }
                                    ?>
                                </div>
                                <input type="hidden" name="iconImage" class="jsev-img-id"  value="<?php echo esc_attr(intval($item['iconImage']));?>"/>

                                <a href="" class="delete-jsev-img<?php if(!$item['iconImage']){ echo ' hidden';}?>"><?php echo esc_html__('Remove image', 'joomsport-sports-league-results-management');?></a>

                            </div>

                            <button class="button<?php if($item['iconImage']){ echo ' hidden';}?>" id="jsEventImage"><?php echo esc_html__('Add image', 'joomsport-sports-league-results-management'); ?></button>

                        </td>
                    </tr>
                    <tr>
                        <td width="200">
                            <?php echo esc_html__('Ordering', 'joomsport-sports-league-results-management')?>
                        </td>
                        <td>
                            <input type="number" name="ordering" value="<?php echo esc_attr($item['ordering'])?>" />
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    }
    public static function joomsport_icons_validate($item)
    {
        $messages = array();

        if (empty($item['iconName'])) $messages[] = __('Name is required', 'joomsport-sports-league-results-management');
        if (empty($item['iconTip'])) $messages[] = __('Tooltip text is required', 'joomsport-sports-league-results-management');
        //if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'custom_table_example');
        //if (!ctype_digit($item['age'])) $messages[] = __('Age in wrong format', 'custom_table_example');
        //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
        //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
        //...

        if (empty($messages)) return true;
        return implode('<br />', $messages);
    }
    public static function getListValues($item){
        global $wpdb;
        $lists = array();




        return $lists;

    }
}
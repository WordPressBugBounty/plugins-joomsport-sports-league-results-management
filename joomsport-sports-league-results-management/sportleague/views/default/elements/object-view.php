<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$copyrightDescr = get_post_meta($rows->object->ID,'_joomsport_venue_copyright',true);
?>
<div class="row">    
    <div class="col-xs-12 rmpadd" style="padding-right:0px;">
        <div class="jsObjectPhoto rmpadd">
            <div class="photoPlayer">

                    <?php echo wp_kses_post(jsHelperImages::getEmblemBig($rows->getDefaultPhoto()));?>

                    <?php
                    if($copyrightDescr){
                        echo '<div class="imgCopyrights">'.wp_kses_post($copyrightDescr).'</div>';
                    }
                    ?>
            </div>    
        </div>
        <?php
        $class = '';
        $extra_fields = jsHelper::getADF($rows->lists['ef']);
        if ($extra_fields) {
            $class = 'well well-sm';
        } else {
            ?>
            <div class="rmpadd" style="padding-right:0px;padding-left:15px;">
                <?php echo wp_kses_post($rows->getDescription());
            ?>
            </div>
            <?php

        }
        ?>
        <div class="<?php echo esc_attr($class);?> pt10 extrafldcn">
            <?php

                echo wp_kses_post($extra_fields);
            ?>
        </div>
    </div>
    <?php if ($extra_fields) {
    ?>
    <div class="col-xs-12 rmpadd" style="padding-right:0px;">
        <?php echo wp_kses_post($rows->getDescription());
    ?>
    </div>
    <?php 
} ?>
</div>    
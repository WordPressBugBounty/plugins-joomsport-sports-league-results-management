<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$stdoptions = '';
 $stdoptions = "std"; 
if($stdoptions == 'std'){
?>
<div class="table-responsive">
<table class="jsBoxStatDIvFE">
  <tbody>
      <?php
        if ($rows->lists['played_matches'] !== null) {
            ?>
            <tr>
                
                <td>
                    <strong><?php echo esc_html__('Match played','joomsport-sports-league-results-management');
            ?></strong>
                </td>
                <td>
                    <?php 
                        echo esc_html($rows->lists['played_matches']);
            ?>
                </td>
            </tr>
            <?php

        }
        if (count($rows->lists['events_col'])) {
            foreach ($rows->lists['events_col'] as $key => $value) {
                if (isset($rows->lists['players']->{$key})) {
                    ?>
                <tr>
                    
                    <td>
                        <?php echo wp_kses_post($value->getEmblem());?>
                        <strong>
                            <?php echo wp_kses_post($value->getEventName());
                    ?>
                        </strong>
                    </td>
                    <td>
                        <?php 

                        if (is_float(floatval($rows->lists['players']->{$key}))) {
                            echo round($rows->lists['players']->{$key}, 3);
                        } else {
                            echo floatval($rows->lists['players']->{$key});
                        }

                    ?>
                    </td>
                </tr>
                <?php

                }
            }
        }
    ?>
  </tbody>
</table>
</div>
<?php
}

if(isset($rows->lists['boxscore']) && $rows->lists['boxscore']){
    echo '<div class="center-block jscenter">
                    <h3>'.esc_html__('Box Score','joomsport-sports-league-results-management').'</h3>
                </div>';
    echo $rows->lists['boxscore'];
}
if(isset($rows->lists['boxscore_matches']) && $rows->lists['boxscore_matches']){
    echo '<div class="center-block jscenter">
                    <h3>'.esc_html__('Match Box Score','joomsport-sports-league-results-management').'</h3>
                </div>';
    echo $rows->lists['boxscore_matches'];
}

?>
    
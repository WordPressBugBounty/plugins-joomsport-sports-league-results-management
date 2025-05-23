<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="table-responsive">
    <?php
    if (count($rows->lists['players_Stat'])) {

        if (isset($lists['pagination']) && $lists['pagination']) {
            $tblid = '';
        }else{
            $tblid = 'id="jstable_plz"';
        }
        ?>
    <form role="form" method="post" lpformnum="1">
    <table class="table table-striped cansorttbl" <?php echo $tblid;?>>
        <thead>
            <tr>
                
                    <?php
                    $dest = (classJsportRequest::get('sortf') == 'post_title') ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
        $class = '';
        if (classJsportRequest::get('sortf') == 'post_title' || classJsportRequest::get('sortf') == '') {
            $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
        }
        ?>
                <th class="<?php echo esc_attr($class)?>">
                <?php 
                    if (isset($lists['pagination']) && $lists['pagination']) {
                        ?>
                    <a href="<?php echo esc_url(classJsportLink::playerlist($rows->season_id, '&sortf=post_title&sortd='.$dest))?>">
                        <span><?php echo esc_html__('Name','joomsport-sports-league-results-management');?></span>
                        <i class="fa"></i>
                    </a>
                    <?php

                    } else {
                        ?>
                        <a href="javascript:void(0);">
                            <span><?php echo esc_html__('Name','joomsport-sports-league-results-management');?></span>
                            <i class="fa"></i>
                        </a>
                    <?php
                    }
        ?>
                </th>
                <?php
                if (isset($lists['pagination']) && $lists['pagination']) {
                    echo '<th>';
                    echo '<span>'.esc_html__('Team','joomsport-sports-league-results-management').'</span>';
                    echo '</th>';
                }
                ?>
                <?php
                if (isset($rows->lists['played_matches_col']) && $rows->lists['played_matches_col']) {
                    $dest = (classJsportRequest::get('sortf') == 'played') ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
                    $class = '';
                    if (classJsportRequest::get('sortf') == 'played') {
                        $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
                    }
                    ?>
                    <th class="jsTextAlignCenter <?php echo esc_attr($class)?>">
                        <?php
                        if (isset($lists['pagination']) && $lists['pagination']) {
                            ?>
                        <a href="<?php echo esc_url(classJsportLink::playerlist($rows->season_id, '&sortf=played&sortd='.$dest))?>"><span><?php echo esc_html($rows->lists['played_matches_col']);
                            ?></span><i class="fa"></i></a>

                        <?php

                        } else {
                            ?>
                        <a href="javascript:void(0);">
                                    
                            <span><?php //echo $rows->lists['played_matches_col'];
                            echo '<img src="'.esc_url(JOOMSPORT_LIVE_URL_IMAGES_DEF.'matches_played.png').'" width="24" class="sub-player-ico" title="'.esc_attr($rows->lists['played_matches_col']).'" alt="'.esc_attr($rows->lists['played_matches_col']).'" />';
                            ?></span><i class="fa"></i>
                        </a>

                        <?php

                        }
                    ?>
                        
                    </th>

                    <?php

                    $dest = (classJsportRequest::get('sortf') == 'played') ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
                    $class = '';
                    if (classJsportRequest::get('sortf') == 'career_minutes') {
                        $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
                    }
                    ?>
                    <th class="jsTextAlignCenter <?php echo esc_attr($class)?>">
                        <?php
                        if (isset($lists['pagination']) && $lists['pagination']) {
                            ?>
                            <a href="<?php echo esc_url(classJsportLink::playerlist($rows->season_id, '&sortf=career_minutes&sortd='.$dest))?>"><span><?php echo esc_html(__('Played minutes','joomsport-sports-league-results-management'));
                                    ?></span><i class="fa"></i></a>

                            <?php

                        } else {
                            ?>
                            <a href="javascript:void(0);">

                            <span><?php //echo $rows->lists['played_matches_col'];
                                echo '<img src="'.esc_url(JOOMSPORT_LIVE_URL_IMAGES_DEF.'matches_played.png').'" width="24" class="sub-player-ico" title="'.esc_attr(__('Played minutes','joomsport-sports-league-results-management')).'" alt="'.esc_attr(__('Played minutes','joomsport-sports-league-results-management')).'" />';
                                ?></span><i class="fa"></i>
                            </a>

                            <?php

                        }
                        ?>

                    </th>

                    <?php

                }

                if (isset($rows->lists['career_head']) && count($rows->lists['career_head'])) {
                    foreach ($rows->lists['career_head'] as $career) {

                        echo '<th class="jsTextAlignCenter">' . wp_kses_post($career) . '</th>';

                    }
                }


                if (count($rows->lists['events_col'])) {
            foreach ($rows->lists['events_col'] as $key => $value) {
                $dest = (classJsportRequest::get('sortf') == $key) ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
                $class = '';
                if (classJsportRequest::get('sortf') == $key) {
                    $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
                }
                ?>
                        <th class="jsTextAlignCenter <?php echo esc_attr($class)?>">
                            <?php
                            if (isset($lists['pagination']) && $lists['pagination']) {
                                ?>
                            <a href="<?php echo esc_url(classJsportLink::playerlist($rows->season_id, '&sortf='.$key.'&sortd='.$dest))?>">
                                <span>
                                    <?php echo wp_kses_post($value->getEmblem());
                                ?>
                                    <?php  if(!$value->getEmblem()){ echo wp_kses_post($value->getEventName());};
                                ?>
                                </span>
                                <i class="fa"></i>
                            </a>
                            <?php

                            } else {
                                ?>
                            <a href="javascript:void(0);">
                                <span>
                                    <?php echo wp_kses_post($value->getEmblem());
                                ?>
                                    <?php  if(!$value->getEmblem()){ echo wp_kses_post($value->getEventName());};
                                ?>
                                </span>
                                <i class="fa"></i>
                            </a>    
                            <?php

                            }
                ?>
                        </th>
                        <?php

            }
        }
        if (isset($rows->lists['ef_table']) && count($rows->lists['ef_table'])) {
            foreach ($rows->lists['ef_table'] as $ef) {
                $key = 'ef_'.$ef->id;
                $value = $ef->name;
                $dest = (classJsportRequest::get('sortf') == $key) ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
                $class = '';
                if (classJsportRequest::get('sortf') == $key) {
                    $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
                }
                ?>
                        <th class="jsTextAlignCenter <?php echo esc_attr($class)?>">
                            <span><?php echo wp_kses_post($value);?></span>
                        </th>
                    <?php

            }
        }
        ?>
            </tr>
        </thead>
        <tbody>
        <?php

        for ($intA = 0; $intA < count($rows->lists['players_Stat']); ++$intA) {
            $playerST = $rows->lists['players_Stat'][$intA];
            
            $playerevents = $playerST->lists['tblevents'];
            ?>

            <tr>
                <td>
                    <div class="jsDivLineEmbl">
                        <?php echo wp_kses_post($playerST->getEmblem(true, 0, ''));?>
                        <?php echo wp_kses_post(jsHelper::nameHTML($playerST->getName(true)));?>
                    </div>

                </td>
                <?php
                if (isset($lists['pagination']) && $lists['pagination']) {
                    echo '<td>';
                    if($playerST->teamID){
                        $teamObj = new classJsportTeam($playerST->teamID, $rows->season_id, false);
                        $title = $teamObj->getName(false, 0, 1);
                        //$teamObj = get_post($playerST->teamID);
                        if($title){
                            echo classJsportLink::team($title,$playerST->teamID,$rows->season_id);
                            
                        }
                    }
                    echo '</td>';
                }
                ?>
                <?php
                if (isset($rows->lists['played_matches_col']) && $rows->lists['played_matches_col']) {
                    ?>
                    <td class="jsTextAlignCenter">
                        <?php
                        echo wp_kses_post($playerevents->played);
                    ?>
                    </td>
                    <td>
                        <?php
                        echo wp_kses_post($playerevents->career_minutes);
                        ?>
                    </td>
                    <?php

                }
            ?>
                <?php
                if(isset($playerST->career) && is_array($playerST->career) && count($playerST->career)) {
                        for ($intC = 0; $intC < count($playerST->career ); $intC++) {
                            echo '<td class="jsTextAlignCenter">' . wp_kses_post($playerST->career[$intC]) . '</td>';
                        }
                        ?>
                        <?php

                }
                ?>
                <?php

                if (count($rows->lists['events_col'])) {
                    foreach ($rows->lists['events_col'] as $key => $value) {
                        ?>
                        <td class="jsTextAlignCenter">
                            <?php
                            if (isset($playerevents->{$key})) {
                                if (is_float(floatval($playerevents->{$key}))) {
                                    echo esc_html(round($playerevents->{$key}, 3));
                                } else {
                                    echo esc_html(floatval($playerevents->{$key}));
                                }
                            }
                        ?>
                            
                        </td>
                        <?php

                    }
                }
            ?>

                <?php
                if (isset($rows->lists['ef_table']) && count($rows->lists['ef_table'])) {
                    foreach ($rows->lists['ef_table'] as $ef) {
                        $key = 'ef_'.$ef->id;
                        $value = $ef->name;
                        ?>
                        <td class="jsTextAlignCenter">
                            <?php
                            if (isset($playerST->fields[$key])) {
                                echo wp_kses_post($playerST->fields[$key]);
                            }
                        ?>
                            
                        </td>
                        <?php

                    }
                }
            ?>
            </tr>
            <?php

        }
        ?>
        </tbody>
    </table>  
        
    
<?php
if (isset($lists['pagination']) && $lists['pagination']) {
    require_once JOOMSPORT_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR.'pagination.php';
    echo paginationView($lists['pagination']);
} else {
    ?>

<?php 
}
        ?>
</form>
    <?php

    }
    ?>
</div>

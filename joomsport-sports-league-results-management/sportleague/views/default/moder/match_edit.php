<?php
?>
<div>
    <?php do_action("joomsport_match_before_tabs", $matchID);?>
    <form action="" name="formMatchEditFE" id="formMatchEditFE">
        <?php
        $canEditMatch = apply_filters( 'joomsport_can_edit_match', true, $matchID);
        if(!$canEditMatch){
            echo '<script>const form = document.querySelector(\'#formMatchEditFE\');
                for (const el of form.elements) {
                  el.disabled = true;
                }
                document.querySelector(\'#sbClose\').disabled = false;
                jQuery(".fa-trash").css("display", "none");
                </script>';
        }

        JoomSportMetaMatch::js_meta_score($teamPost);
        JoomSportMetaMatch::js_meta_general($teamPost);
        JoomSportMetaMatch::js_meta_ef($teamPost);
        JoomSportMetaMatch::js_meta_about($teamPost);
        JoomSportMetaMatch::js_meta_playerevents($teamPost);
        JoomSportMetaMatch::js_meta_mevents($teamPost);
        JoomSportMetaMatch::js_meta_lineup($teamPost);
        JoomSportMetaMatch::js_meta_subs($teamPost);
        ?>

        <div class="jsmodsave pull-right clearfix">
            <input type="submit" name="sbSavNoClose" class="btn btn-success" value="<?php echo esc_attr(__('Save','joomsport-sports-league-results-management'));?>" />
            <input type="submit" name="sbSavClose" class="btn btn-success" value="<?php echo esc_attr(__('Save & Close','joomsport-sports-league-results-management'));?>" />
            <input type="submit" name="sbClose" id="sbClose" class="btn" value="<?php echo esc_attr(__('Close','joomsport-sports-league-results-management'));?>" />

            <input type="hidden" name="matchID" value="<?php echo esc_attr($matchID);?>" />
        </div>
    </form>
</div>

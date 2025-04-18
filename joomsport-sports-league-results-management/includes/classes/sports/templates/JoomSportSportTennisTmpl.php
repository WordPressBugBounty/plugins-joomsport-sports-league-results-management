<?php
require_once JOOMSPORT_PATH_INCLUDES.'classes'.DIRECTORY_SEPARATOR.'sports'.DIRECTORY_SEPARATOR.'joomsport-sport-common.php';

class JoomSportSportTennisTmpl extends joomsportSportCommon
{
    public static function getScoreMatchBE($args){
        $score = get_post_meta($args["id"], "_joomsport_match_tennisscore", true);

        if(!$score){
            $score = array(array("tHomescore"=>"","tAwayscore"=>""),array("tHomescore"=>"","tAwayscore"=>""),array("tHomescore"=>"","tAwayscore"=>""));
        }
        ?>
        <div class="jstable jsminwdhtd beScoreMatchTennisCont">
            <div class="jstable-row">
                <div class="jstable-cell" style="width:200px;">
                    <div><?php echo esc_html($args["hTeam"])?></div>
                    <div><?php echo esc_html($args["aTeam"])?></div>
                </div>
                <?php
                for($intA=0;$intA<count($score);$intA++){
                    ?>
                    <div class="jstable-cell">
                        <div>
                            <input type="number" name="tHomescore[]" class="form-control" style="max-width:50px;" value="<?php echo isset($score[$intA]["tHomescore"])?$score[$intA]["tHomescore"]:"";?>" />
                            <input type="number" name="tHomescoreExp[]" class="jsScrHmVExp" value="<?php echo isset($score[$intA]["tHomescoreExp"])?$score[$intA]["tHomescoreExp"]:"";?>" />

                        </div>
                        <div>
                            <input type="number" name="tAwayscore[]" class="form-control" style="max-width:50px;" value="<?php echo isset($score[$intA]["tAwayscore"])?$score[$intA]["tAwayscore"]:"";?>" />
                            <input type="number" name="tAwayscoreExp[]" class="jsScrHmVExp" value="<?php echo isset($score[$intA]["tAwayscoreExp"])?$score[$intA]["tAwayscoreExp"]:"";?>" />

                        </div>
                    </div>
                    <?php
                }
                ?>

                <div class="jstable-cell">
                    <input type="button" value="+" class="btnAddTenSet" />
                </div>
            </div>
        </div>
        <?php
    }

    public static function saveScoreMatchBE($args){
        $post = $args["post"];
        $post_id = $args["post_id"];

        $score1 = $score2 = 0;
        $scorejson = array();
        for($intA=0;$intA<count($post["tHomescore"]);$intA++){
            if($post["tHomescore"][$intA]!='' && isset($post["tAwayscore"][$intA]) && $post["tAwayscore"][$intA]!=''){
                $scorejson[] = array("tHomescore"=>$post["tHomescore"][$intA],"tAwayscore"=>$post["tAwayscore"][$intA],"tHomescoreExp"=>$post["tHomescoreExp"][$intA],"tAwayscoreExp"=>$post["tAwayscoreExp"][$intA]);
                if(intval($post["tHomescore"][$intA]) > intval($post["tAwayscore"][$intA])){
                    $score1++;
                }elseif(intval($post["tHomescore"][$intA]) < intval($post["tAwayscore"][$intA])){
                    $score2++;
                }
            }
        }
        update_post_meta($post_id, '_joomsport_home_score', $score1);
        update_post_meta($post_id, '_joomsport_away_score', $score2);


        update_post_meta($post_id, '_joomsport_match_tennisscore', $scorejson);

    }
    public static function getScoreFE($match)
    {
        $width = JoomsportSettings::get('set_emblemhgonmatch', 60);
        $partic_home = $match->getParticipantHome();
        $partic_away = $match->getParticipantAway();
        $score = get_post_meta($match->id, "_joomsport_match_tennisscore", true);

        ?>

        <div class="row">
            <div class="jstable">
                <div class="jstable-row">
                    <div class="jstable-cell">
                        <?php echo $partic_home ? wp_kses_post($partic_home->getEmblem(true, 0, 'emblInline', $width)) : ''; ?>

                    </div>
                    <div class="jstable-cell">
                        <span>
                            <?php echo ($partic_home) ? wp_kses_post($partic_home->getName(true)) : ''; ?>
                        </span>
                    </div>
                    <?php
                    for($intA=0;$intA<count($score);$intA++){
                        $tclass = (isset($score[$intA]["tHomescore"]) && isset($score[$intA]["tAwayscore"]))?($score[$intA]["tHomescore"]>$score[$intA]["tAwayscore"]?" tenSetWinner":""):"";
                        ?>
                        <div class="jstable-cell">
                            <div class="tennisSetDiv<?php echo $tclass;?>">
                                <?php echo isset($score[$intA]["tHomescore"])?$score[$intA]["tHomescore"]:"";?>
                                <sup><?php echo isset($score[$intA]["tHomescoreExp"])?$score[$intA]["tHomescoreExp"]:"";?></sup>

                            </div>

                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="jstable-row">
                    <div class="jstable-cell">
                        <?php echo $partic_away ? wp_kses_post($partic_away->getEmblem(true, 0, 'emblInline', $width)) : ''; ?>

                    </div>
                    <div class="jstable-cell">
                        <span>
                            <?php echo ($partic_away) ? wp_kses_post($partic_away->getName(true)) : ''; ?>
                        </span>
                    </div>
                    <?php
                    for($intA=0;$intA<count($score);$intA++){
                        $tclass = (isset($score[$intA]["tHomescore"]) && isset($score[$intA]["tAwayscore"]))?($score[$intA]["tHomescore"]<$score[$intA]["tAwayscore"]?" tenSetWinner":""):"";

                        ?>
                        <div class="jstable-cell">
                            <div  class="tennisSetDiv<?php echo $tclass;?>">
                                <?php echo isset($score[$intA]["tAwayscore"])?$score[$intA]["tAwayscore"]:"";?>
                                <sup><?php echo isset($score[$intA]["tAwayscoreExp"])?$score[$intA]["tAwayscoreExp"]:"";?></sup>

                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>

            </div>

        </div>
        <?php
    }

    public static function getScoreFESmall($args){
        $score = get_post_meta($args["match"]->id, "_joomsport_match_tennisscore", true);

        $html = '<div class="jstable">';
        $html .= '<div class="jstable-row">';
        for($intA=0;$intA<count($score);$intA++){
            $tclass = (isset($score[$intA]["tHomescore"]) && isset($score[$intA]["tAwayscore"]))?($score[$intA]["tHomescore"]>$score[$intA]["tAwayscore"]?" tenSetWinner":""):"";

            $html .= ' <div class="jstable-cell">';
            $html .= '<div class="tennisSetDiv'.$tclass.'">';

            $html .=  isset($score[$intA]["tHomescore"])?$score[$intA]["tHomescore"]:"";
            $html .= '<sup>'.(isset($score[$intA]["tHomescoreExp"])?$score[$intA]["tHomescoreExp"]:"").'</sup>';
            $html .= '</div>';

            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '<div class="jstable-row">';

        for($intA=0;$intA<count($score);$intA++){
            $tclass = (isset($score[$intA]["tHomescore"]) && isset($score[$intA]["tAwayscore"]))?($score[$intA]["tHomescore"]<$score[$intA]["tAwayscore"]?" tenSetWinner":""):"";

            $html .= ' <div class="jstable-cell">';
            $html .= '<div class="tennisSetDiv'.$tclass.'">';
            $html .=  isset($score[$intA]["tAwayscore"])?$score[$intA]["tAwayscore"]:"";
            $html .= '<sup>'.(isset($score[$intA]["tAwayscoreExp"])?$score[$intA]["tAwayscoreExp"]:"").'</sup>';
            $html .= '</div>';
            $html .= '</div>';

        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;

    }

    public static function getScoreModuleScoreHome($args){
        $match = $args["match"];
        $score = get_post_meta($match->id, "_joomsport_match_tennisscore", true);

        for($intA=0;$intA<count($score);$intA++){
            $tclass = (isset($score[$intA]["tHomescore"]) && isset($score[$intA]["tAwayscore"]))?($score[$intA]["tHomescore"]>$score[$intA]["tAwayscore"]?" tenSetWinner":""):"";
            ?>
            <td width="30">
                <div class="tennisSetDiv<?php echo $tclass;?>">
                    <?php echo isset($score[$intA]["tHomescore"])?$score[$intA]["tHomescore"]:"";?>
                    <sup><?php echo isset($score[$intA]["tHomescoreExp"])?$score[$intA]["tHomescoreExp"]:"";?></sup>

                </div>

            </td>
            <?php
        }

    }
    public static function getScoreModuleScoreAway($args){
        $match = $args["match"];
        $score = get_post_meta($match->id, "_joomsport_match_tennisscore", true);

        for($intA=0;$intA<count($score);$intA++){
            $tclass = (isset($score[$intA]["tHomescore"]) && isset($score[$intA]["tAwayscore"]))?($score[$intA]["tHomescore"]<$score[$intA]["tAwayscore"]?" tenSetWinner":""):"";
            ?>
            <td width="30">
                <div class="tennisSetDiv<?php echo $tclass;?>">
                    <?php echo isset($score[$intA]["tAwayscore"])?$score[$intA]["tAwayscore"]:"";?>
                    <sup><?php echo isset($score[$intA]["tAwayscoreExp"])?$score[$intA]["tAwayscoreExp"]:"";?></sup>

                </div>

            </td>
            <?php
        }
    }
    public static function getScoreHTMLHelper($args){
        $match = $args["match"];
        $htmlLive = '';
        if($args["m_played"] == -1){
            $liveWrd = __("Live", 'joomsport-sports-league-results-management' );
            $ticker_html = jsHelper::matchTicker($match->id);
            $htmlLive = '<div class="jscalendarLive">'.($ticker_html?$ticker_html:$liveWrd).'</div>';
        }
        return '<div class="'.$args["class"].'" data-toggle2="tooltip" data-placement="bottom" title="" data-original-title="'.htmlspecialchars(($args["tooltip"])).'">'.$htmlLive.$args["html"].$match->getETLabel().'</div>'.$match->getBonusLabel();

    }
}
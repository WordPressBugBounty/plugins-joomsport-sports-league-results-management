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
<form role="form" method="post" lpformnum="1">
    <div class="searchMatchesDiv">
        <div>
            
            <div class="searchBar col-xs-12 col-lg-12">
                <?php if (isset($lists['filters']) && $lists['enable_search'] == '1') {
    ?>
                    <div <?php echo ($lists['apply_filters'] == true) ? ' style="display:block;"' : 'style="display:none;"';
    ?> id="jsFilterMatches">

                        
                          <div class="form-group srcTeam">
                            <label for="partic"><?php echo esc_html__('Participant','joomsport-sports-league-results-management');
    ?></label>
                            <select name="filtersvar[partic]" id="partic" >
                              <option value="0"><?php echo esc_html__('All','joomsport-sports-league-results-management');
    ?></option>
                              <?php
                              if (isset($lists['filters']['partic_list']) && count($lists['filters']['partic_list'])) {
                                  foreach ($lists['filters']['partic_list'] as $key => $value) {
                                      echo '<option value="'.esc_attr($key).'" '.((isset($lists['filtersvar']->partic) && $lists['filtersvar']->partic == $key) ? 'selected' : '').'>'.wp_kses_post($value).'</option>';
                                  }
                              }
    ?>
                            </select>
                            <select name="filtersvar[place]" style="width:80px;" >
                              <option value="0"><?php echo esc_html__('All','joomsport-sports-league-results-management');
    ?></option>
                              <option value="1" <?php echo  (isset($lists['filtersvar']->place) && $lists['filtersvar']->place == 1) ? 'selected' : ''?>><?php echo esc_html__('H','joomsport-sports-league-results-management');
    ?></option>
                              <option value="2" <?php echo  (isset($lists['filtersvar']->place) && $lists['filtersvar']->place == 2) ? 'selected' : ''?>><?php echo esc_html__('A','joomsport-sports-league-results-management');
    ?></option>
                            </select>
                          </div>
                          <div class="form-group srcDay">
                            <label for="matchDay"><?php echo esc_html__('Matchday','joomsport-sports-league-results-management');
    ?></label>
                            <select name="filtersvar[mday]" id="matchDay">
                              <option value="0"><?php echo esc_html__('All','joomsport-sports-league-results-management');
    ?></option>
                              <?php
                              if (isset($lists['filters']['mday_list']) && count($lists['filters']['mday_list'])) {
                                  foreach ($lists['filters']['mday_list'] as $mday) {
                                      echo '<option value="'.esc_attr($mday->id).'" '.((isset($lists['filtersvar']->mday) && $lists['filtersvar']->mday == $mday->id) ? 'selected' : '').'>'.esc_html($mday->m_name).'</option>';
                                  }
                              }
    ?>
                            </select>
                          </div>
                          <div class="form-group srcDate">
                            <label for="date_from"><?php echo esc_html__('Date','joomsport-sports-league-results-management');
    ?></label>
                            
                              <input type="text" class="jsdatefield" name="filtersvar[date_from]" value="<?php echo  (isset($lists['filtersvar']->date_from) && $lists['filtersvar']->date_from) ? esc_attr($lists['filtersvar']->date_from) : ''?>" class="form-control " id="date_from" placeholder="">
                                <input type="text" class="jsdatefield" name="filtersvar[date_to]" value="<?php echo  (isset($lists['filtersvar']->date_to) && $lists['filtersvar']->date_to) ? esc_attr($lists['filtersvar']->date_to) : ''?>" class="form-control" id="date_to" placeholder="">
                            
                          </div>
                          <div class="form-group">
                              <button type="button" class="btn btn-default pull-right" onclick="javascript:this.form.submit();"><i class="fa fa-search"></i><?php echo esc_html__('Search','joomsport-sports-league-results-management');
    ?></button>
                          </div>
                    </div>
                <?php 
} ?>
            </div>        
            
        </div>
    </div>
    <div class="table-responsive">
        <?php
        echo jsHelper::getMatches($rows, $lists);
        ?>
    </div>
</form>
     <div class="jsClear"></div>
    <?php if (JoomsportSettings::get('jsbrand_on',1) == 1):?>
    <br />
    <div id="copy" class="copyright">powered by <a href="https://joomsport.com">JoomSport: Sports team management app</a></div>
    <?php endif;?>
     <div class="jsClear"></div>
</div>

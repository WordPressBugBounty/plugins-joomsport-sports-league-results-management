<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class JoomsportPageHelp{
    public static function action(){
        ?>
<script type="text/javascript" id="UR_initiator"> (function () { var iid = 'uriid_'+(new Date().getTime())+'_'+Math.floor((Math.random()*100)+1); if (!document._fpu_) document.getElementById('UR_initiator').setAttribute('id', iid); var bsa = document.createElement('script'); bsa.type = 'text/javascript'; bsa.async = true; bsa.src = '//beardev.useresponse.com/sdk/supportCenter.js?initid='+iid+'&wid=6'; (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(bsa); })(); </script>
        <div class='jsrespdiv10'>
            <div class="jsrespdiv12">
                <div class="jsBepanel">
                    <?php
                    require_once JOOMSPORT_PATH_HELPERS . 'tabs.php';
                    $etabs = new esTabs();
                    ?>
                    <div class="jsBEsettings" style="padding:0px;">
                <!-- <tab box> -->
                        <ul class="tab-box">
                                <?php
                                echo wp_kses_post($etabs->newTab(esc_html__('Support','joomsport-sports-league-results-management'), 'main_conf', '', 'vis'));
                                echo wp_kses_post($etabs->newTab(esc_html__('About','joomsport-sports-league-results-management'), 'about_conf', ''));

                                ?>
                        </ul>	
                        <div style="clear:both"></div>
                    </div>
                </div>    
            </div>
            <div id="main_conf_div" class="tabdiv">
                <div class="jsrespdiv6">
                    <div class="jsBepanel">

                        <div class="jsBEheader">
                            <?php echo esc_html__('Documentation','joomsport-sports-league-results-management'); ?>
                        </div>

                        <div class="jsBEsettings jsLinks" onclick="location.href='https://joomsport.com/support/documentation/joomsport-wordpress-sports-plugin-documentation.html'">
                            <div class="jhelpicons"><img src="<?php echo esc_url(plugins_url( '../../assets/images/documentation.png', __FILE__ ))?>"></div>
                            <div class="jhelpdescr"><?php echo esc_html__('JoomSport User manual','joomsport-sports-league-results-management'); ?></div>
                        </div>

                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('FAQ','joomsport-sports-league-results-management'); ?>
                        </div>
                        <div class="jsBEsettings jsLinks" onclick="location.href='https://joomsport.com/support/faq.html'">
                            <div class="jhelpicons"><img src="<?php echo esc_url(plugins_url( '../../assets/images/faq.png', __FILE__ ))?>"></div>
                            <div class="jhelpdescr"><?php echo esc_html__('Answers to the most commonly asked questions','joomsport-sports-league-results-management'); ?></div>
                        </div>
                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('Live Chat','joomsport-sports-league-results-management'); ?>
                        </div>
                        <div class="jsBEsettings jsLinks"  onclick="location.href='https://www.joomsport.com'">
                            <div class="jhelpicons"><img src="<?php echo esc_url(plugins_url( '../../assets/images/chat.png', __FILE__ ))?>"></div>
                            <div class="jhelpdescr"><?php echo esc_html__('JoomSport live chat is available','joomsport-sports-league-results-management')?>:
                                8-10a.m (GMT+0)
                                1-3 p.m (GMT+0)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jsrespdiv6 jsrespmarginleft2">
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('Forum','joomsport-sports-league-results-management'); ?>
                        </div>
                        <div class="jsBEsettings jsLinks"  onclick="location.href='https://joomsport.com/support/forum.html'">
                            <div class="jhelpicons"><img src="<?php echo esc_url(plugins_url( '../../assets/images/forum.png', __FILE__ ))?>"></div>
                            <div class="jhelpdescr"><?php echo esc_html__('Officially supported multilingual forums','joomsport-sports-league-results-management'); ?></div>
                        </div>
                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('HelpDesk','joomsport-sports-league-results-management'); ?>
                        </div>
                        <div class="jsBEsettings jsLinks"  onclick="location.href='https://joomsport.com/support/helpdesk.html'">
                            <div class="jhelpicons"><img src="<?php echo esc_url(plugins_url( '../../assets/images/support.png', __FILE__ ))?>"></div>
                            <div class="jhelpdescr"><?php echo esc_html__('Service for technical and general enquiries','joomsport-sports-league-results-management'); ?></div>
                        </div>
                    </div>
                </div>
                <div style="clear:both;"></div>
            </div>
            <div id="about_conf_div" class="tabdiv visuallyhidden">
                <div class="jsrespdiv6">
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo esc_html__('Product information','joomsport-sports-league-results-management'); ?>
                        </div>
                        <div class="jsBEsettings">
                            <table class="jsHelpinfoTbl">
                                <tr>
                                    <td>
                                        <?php echo esc_html__('Edition');?>:
                                    </td>
                                    <td>
                                       <?php
                                       echo 'JoomSport ';
                                       
                                       ?>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>

                                        <?php echo esc_html__('Copyright','joomsport-sports-league-results-management');?>:

                                    </td>

                                    <td>

                                            &copy; BearDev

                                    </td>
                                </tr>
                                <tr>

                                    <td>

                                            <?php echo esc_html__('Main site','joomsport-sports-league-results-management');?>:

                                    </td>

                                    <td>

                                            <a href="http://www.JoomSport.com">www.JoomSport.com</a>

                                    </td>

                                </tr>
                                <tr>

                                    <td>

                                            <?php echo esc_html__('Developer','joomsport-sports-league-results-management');?>:

                                    </td>

                                    <td>

                                            <a href="http://www.beardev.com" target="_blank">BearDev web development company</a>

                                    </td>

                                </tr>
                                <?php
                                
                                ?>
                                <tr>

                                    <td>

                                            <?php echo esc_html__('Trademarks','joomsport-sports-league-results-management');?>:

                                    </td>

                                    <td>

                                            <a href="http://joomsport.com/joomsport-trademarks.html" target="_blank">JoomSport trademarks policy</a>

                                    </td>

                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <br /><br />
                <div style="clear:both;"></div>
                <div class="jswp_social" style="clear:both; margin-top:40px; text-align:center;">
               
        <br />            Follow us on Twitter 
                    <a style="margin-right: 15px;" href="https://twitter.com/beardev" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                    become a fan on Facebook
                    <a style="margin-right: 15px;" href="https://www.facebook.com/pages/BearDev/130697180026" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                      or subscribe to our Blog 
                    <a href=" http://beardev.com/blog" target="_blank"> <img src=" http://beardev.com/images/130x130-logo_beardev-latest.png " style="height:21px;" /></a>
                </div>

            <input type="hidden" name="jscurtab" id="jscurtab" value="" />
        </div>
        <?php
    }
}
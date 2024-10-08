<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_MODELS.'model-jsport-venue.php';

class classJsportVenue
{
    private $id = null;
    private $season_id = null;
    public $object = null;
    public $lists = null;

    const VIEW = 'common';

    public function __construct($id = 0, $season_id = null, $loadLists = true)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->id = get_the_ID();
        } else {
            $this->season_id = $season_id;
            $this->id = $id;
        }
        if (!$this->id) {
            die('ERROR! Venue ID not DEFINED');
        }
        $this->loadObject($loadLists);
    }

    private function loadObject($loadLists)
    {
        $obj = new modelJsportVenue($this->id, $this->season_id);
        $this->object = $obj->getRow();
        if ($this->object && $loadLists) {
            $this->lists = $obj->loadLists();
        }
    }

    public function getObject()
    {
        $this->setHeaderOptions();

        return $this->object;
    }

    public function getName($linkable = false)
    {
        $html = '';
        $pp = get_post($this->id);
        if ($pp->post_status != 'publish' || get_post_status($this->id) == 'private') {
            $linkable = false;
        }
        if (!$this->object) {
            return '';
        }
        if (!$linkable) {
            return $this->object->post_title;
        }
        if ($this->id > 0) {
            $html = classJsportLink::venue($this->object->post_title, $this->id, false, '');
        }

        return $html;
    }

    public function getDefaultPhoto()
    {

        if ($this->lists['def_img']) {
            return $this->lists['def_img'];
        }

        return JOOMSPORT_LIVE_URL_IMAGES_DEF.JSCONF_VENUE_DEFAULT_IMG;
    }

    public function getRow()
    {
        return $this;
    }
    public function getDescription()
    {
        $descr = get_post_meta($this->id,'_joomsport_venue_about',true);
 
        return classJsportText::getFormatedText($descr).$this->getVenueLocation();
    }
    public function getView()
    {
        return self::VIEW;
    }

    public function getTabs()
    {
        $tabs = array();
        $intA = 0;
        //main tab
        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = __('Overview','joomsport-sports-league-results-management');
        $tabs[$intA]['body'] = 'object-view.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'js-venue';

        //photos
        if (count($this->lists['photos']) > 1) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_photos';
            $tabs[$intA]['title'] = __('Photos','joomsport-sports-league-results-management');
            $tabs[$intA]['body'] = 'gallery.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'js-photo';
        }
        
        if ( has_filter( 'joomsport_custom_tab_fe' ) ){
            $tabs = apply_filters("joomsport_custom_tab_fe", $this->id, $tabs);
        }

        return $tabs;
    }

    public function getVenueLocation()
    {
        $metadata = get_post_meta($this->id,'_joomsport_venue_personal',true);
        $html = '';
        if (isset($metadata['latitude']) && isset($metadata['longitude'])) {
            $html .= '<div class="map">';

            if ($metadata['latitude'] && $metadata['longitude']) {
                $html .= '<div id="venue_gmap"></div>
                ';
                echo '<script type="text/javascript">
                    function initializeJsMp() {
                        var myLatlng = new google.maps.LatLng('.esc_js($metadata['latitude']).', '.esc_js($metadata['longitude']).');

                        var myOptions = {
                            zoom: 12,
                            center: myLatlng,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        }
                        var map = new google.maps.Map(document.getElementById("venue_gmap"), myOptions);
                        var marker = new google.maps.Marker({
                            position: myLatlng,
                            title:"'.esc_js(htmlspecialchars($this->object->post_title)).'"
                        });

                        // To add the marker to the map, call setMap();
                        marker.setMap(map);
                    }

                    function loadScriptik() {
                        var script = document.createElement("script");
                        script.type = "text/javascript";
                        script.src = "https://maps.google.com/maps/api/js?callback=initializeJsMp&key='.esc_js(JSCONF_GMAP_API_KEY).'";
                        document.body.appendChild(script);
                    }

                    window.onload = loadScriptik;
                </script>';
            }

            $html .= '</div>';
        }

        return $html;
    }
    public function setHeaderOptions()
    {

        if (JoomsportSettings::get('jsbp_venue') == '1') {
            $this->lists['options']['social'] = true;
            $img = $this->getDefaultPhoto();

        }
    }
}

<?php



/*******************************************************************************
*  Video Rendering Functions
*******************************************************************************/

function teacherstv_embed($video_id, $embed_type = "flash", $embed_width=640, $embed_height=480, $embed_preload = TRUE, $embed_controls = TRUE, $embed_autoplay = FALSE) {
  // let's check to see if the video's been retrieved yet.

  $video_details = teacherstv_get_video($video_id);

    // what kind of embed code are we using?
    switch ($embed_type) {

      // returns a flash object.
      case 'flash':
      default:
        $output = '
        <object width="' . $embed_width . '" height="' . round($embed_height) . '" data="' . teacherstv_get_flash_url(variable_get('teacherstv_apiurl', ''), $video_details->access_key) . '" type="application/x-shockwave-flash">
          <param name="allowScriptAccess" value="never">
          <param name="allowNetworking" value="internal">
          <param name="wmode" value="opaque">
          <param name="movie" value="' . teacherstv_get_flash_url(variable_get('teacherstv_apiurl', ''), $video_details->access_key) .  '">
          <param name="allowFullScreen" value="true">
          <embed src="' . teacherstv_get_flash_url(variable_get('teacherstv_apiurl', ''), $video_details->access_key) . '" type="application/x-shockwave-flash" width="' . $embed_width . '" height="' . round($embed_height) . '" allowscriptaccess="never" allownetworking="internal">
            <p class="flash_js_notice" style="background: #ffffcc; color: black !important; text-align: center;">
              <a href="http://get.adobe.com/flashplayer/">Flash Player is required to view this file / Flash Player est nécessaire pour afficher ce fichier</a>.
              </p>
        </object>';
        unset($video_details);
        break;

      // Returns an h.264 video object.  Please see the following URL for
      // current browser support:
      //
      // http://diveintohtml5.org/video.html
      case 'mobile':
        $output = '
        <video src="' . teacherstv_get_mp4_url($video_details->video_url, $video_details->access_key) . '" width="' . round($embed_width) . '" height="' . round($embed_height) . '"';

        if (isset($embed_preload) && $embed_preload == TRUE) {
          $output .= " preload";
        }

        if (isset($embed_controls) && $embed_controls == TRUE) {
          $output .= " controls";
        }

        if (isset($embed_autoplay) && $embed_autoplay == TRUE) {
          $output .= " autoplay";
        }
        $output .= '></video>';
      break;

      // returns a hybrid html5 object with Android support and Flash fallback;
      // this still isn't supported by Firefox. Oh, well.
      case 'hybrid':

        $output = '<video id="movie" width="' . $embed_width . '" height="' . $embed_height . '" preload controls>
            <source src="' . teacherstv_get_mp4_url($video_details->video_url, $video_details->access_key) . '" type="video/mp4"/>
            <object width="' . $embed_width . '" height="' . $embed_height . '" type="application/x-shockwave-flash"
              data="flowplayer-3.2.1.swf">
              <param name="movie" value="flowplayer-3.2.1.swf" />
              <param name="allowfullscreen" value="true" />
              <param name="flashvars" value=\'config={"clip": {"url": "' . teacherstv_get_flash_url(variable_get('teacherstv_apiurl', ''), $access_key) . '", "autoPlay":false, "autoBuffering":true}}\' />
            </object>
          </video>
          <script>
            var v = document.getElementById("movie");
            v.onclick = function() {
              if (v.paused) {
                v.play();
              } else {
                v.pause();
              }
            };
          </script>';
      break;

    }

    return $output;

}

/**
 * Helper function to return the flash URL for a video object.
 *
 * @param $api_url string
 *   URL for the Core Catalyst API.
 * @param $access_key string
 *   Access key for the video, supplied by the access_key attribute.
 *
 * @return mixed
 *   Returns a string with the URL for the Flash video or false.
 * if an error occurs.
 */
function teacherstv_get_flash_url($api_url, $access_key) {

  // we don't want pesky trailing slashes at the end of the URL.
  $api_url = rtrim($api_url,"/");

  // remove the index from the API URL if it's there
  $flash_url = str_replace('/api/video/index','/v/',$api_url) . $access_key;

  return $flash_url;

}

/**
 * Helper function to return the specific URL to the h.264 MP4 file for a
 * video.  This is useful in a HTML5 context, where we want to use the
 * <video> tag.  This is mostly used for mobile devices, such as iOS or
 * Android, that make use of h.264.
 *
 * At the moment, we have to build the MP4 url programmatically from the
 * $this->video_url attribute (this is a link to the SIML file). In the
 * future, the API will hopefully supply a direct URL.
 *
 * @param $video_url string
 *   URL for the video, supplied by the video_url attribute.
 * @param $access_key string
 *   Access key for the video, supplied by the access_key attribute.
 *
 * @return mixed
 *   returns a string with the URL for the Flash video or false
 *   if an error occurs.
 */
function  teacherstv_get_mp4_url($video_url, $access_key) {

  // replace video_url RTMP location with HTTP location.
  $mp4_url = str_replace('rtmp://fms', 'http://wpc', $video_url);

  return $mp4_url;

}

function teacherstv_detect_mobile() {
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
        return TRUE;
    }
    return FALSE;
}


/**
  * Builds HTML for a specific video
  *
  * @param video_id int
  *   The ID of the video to render
  *
  * @return string
  *   The HTML object to display in-page.
  */
function teacherstv_render_video($video_id) {

  $video = teacherstv_get_video($video_id);

  $status = $video->video_status;
  $embed_width = variable_get('teacherstv_videowidth', '480');
  $embed_height = variable_get('teacherstv_videoheight', '320');
  $html = '';
  $errors = array();

  if ($status != 5) {
    // the video is available for embedding
    $errors[] = t('Sorry, we are experiencing technical difficulties with this video.  Please try viewing this video at a later time.');
    if (user_access('Administer Corevideofield')) {
      $video = new coreapi();
      $errors[] = t('Current status: @status', array('@status' => $video->video_status_code[$status]));
      unset($video);
    }
  }

      if (count($errors) > 0) {
        // we don't get to see the video
        // set any of our errors
        $html .= "<div class=\"messages error\">";
        $html .= "<ul>";
        foreach ($errors as $error) {
            $html .= "<li>" . $error . "</li>";
        }
        $html .= "</ul>";
        $html .= "</div>";
      }
      else {
        // there are no errors, we do get to see the video
        // detect user agent and set the embed type
        if (variable_get('teacherstv_mobileready',FALSE) && teacherstv_detect_mobile()) {
          $embed_type = 'mobile';
          $embed_preload = TRUE;
          $embed_autoplay = FALSE;
          $embed_controls = TRUE;
        }
        else {
          $embed_type = 'flash';
          $embed_preload = TRUE;
          $embed_autoplay = FALSE;
          $embed_controls = TRUE;
        }
        $html .= "<div class=\"teacherstv_embed\">" . teacherstv_embed($video->video_id, $embed_type, $embed_width, $embed_height, $embed_preload, $embed_controls, $embed_autoplay) . "</div>";
      }

    return $html;
}


/**
 * This is a helper function to return a list of country codes and countries.
 * This function is used for geo-restriction, to provide the user with a
 * friendly list of countries to choose from.
 *
 * @param none
 *
 * @return array
 *   Array of countries and country codes.
 */
function teacherstv_get_countries_list() {

        static $countries;

        if (isset($countries)) {
            return $countries;
        }
        $t = get_t();

        $countries = array(
            'AD' => "Andorra",
            'AE' => "United Arab Emirates",
            'AF' => "Afghanistan",
            'AG' => "Antigua and Barbuda",
            'AI' => "Anguilla",
            'AL' => "Albania",
            'AM' => "Armenia",
            'AN' => "Netherlands Antilles",
            'AO' => "Angola",
            'AP' => "Asia/Pacific Region",
            'AQ' => "Antarctica",
            'AR' => "Argentina",
            'AS' => "American Samoa",
            'AT' => "Austria",
            'AU' => "Australia",
            'AW' => "Aruba",
            'AX' => "Aland Islands",
            'AZ' => "Azerbaijan",
            'BA' => "Bosnia and Herzegovina",
            'BB' => "Barbados",
            'BD' => "Bangladesh",
            'BE' => "Belgium",
            'BF' => "Burkina Faso",
            'BG' => "Bulgaria",
            'BH' => "Bahrain",
            'BI' => "Burundi",
            'BJ' => "Benin",
            'BM' => "Bermuda",
            'BN' => "Brunei Darussalam",
            'BO' => "Bolivia",
            'BR' => "Brazil",
            'BS' => "Bahamas",
            'BT' => "Bhutan",
            'BV' => "Bouvet Island",
            'BW' => "Botswana",
            'BY' => "Belarus",
            'BZ' => "Belize",
            'CA' => "Canada",
            'CC' => "Cocos (Keeling) Islands",
            'CD' => "Congo, The Democratic Republic of the",
            'CF' => "Central African Republic",
            'CG' => "Congo",
            'CH' => "Switzerland",
            'CI' => "Cote d'Ivoire",
            'CK' => "Cook Islands",
            'CL' => "Chile",
            'CM' => "Cameroon",
            'CN' => "China",
            'CO' => "Colombia",
            'CR' => "Costa Rica",
            'CU' => "Cuba",
            'CV' => "Cape Verde",
            'CX' => "Christmas Island",
            'CY' => "Cyprus",
            'CZ' => "Czech Republic",
            'DE' => "Germany",
            'DJ' => "Djibouti",
            'DK' => "Denmark",
            'DM' => "Dominica",
            'DO' => "Dominican Republic",
            'DZ' => "Algeria",
            'EC' => "Ecuador",
            'EE' => "Estonia",
            'EG' => "Egypt",
            'EH' => "Western Sahara",
            'ER' => "Eritrea",
            'ES' => "Spain",
            'ET' => "Ethiopia",
            'EU' => "Europe",
            'FI' => "Finland",
            'FJ' => "Fiji",
            'FK' => "Falkland Islands (Malvinas)",
            'FM' => "Micronesia, Federated States of",
            'FO' => "Faroe Islands",
            'FR' => "France",
            'GA' => "Gabon",
            'GB' => "United Kingdom",
            'GD' => "Grenada",
            'GE' => "Georgia",
            'GF' => "French Guiana",
            'GG' => "Guernsey",
            'GH' => "Ghana",
            'GI' => "Gibraltar",
            'GL' => "Greenland",
            'GM' => "Gambia",
            'GN' => "Guinea",
            'GP' => "Guadeloupe",
            'GQ' => "Equatorial Guinea",
            'GR' => "Greece",
            'GS' => "South Georgia and the South Sandwich Islands",
            'GT' => "Guatemala",
            'GU' => "Guam",
            'GW' => "Guinea-Bissau",
            'GY' => "Guyana",
            'HK' => "Hong Kong",
            'HM' => "Heard Island and McDonald Islands",
            'HN' => "Honduras",
            'HR' => "Croatia",
            'HT' => "Haiti",
            'HU' => "Hungary",
            'ID' => "Indonesia",
            'IE' => "Ireland",
            'IL' => "Israel",
            'IM' => "Isle of Man",
            'IN' => "India",
            'IO' => "British Indian Ocean Territory",
            'IQ' => "Iraq",
            'IR' => "Iran, Islamic Republic of",
            'IS' => "Iceland",
            'IT' => "Italy",
            'JE' => "Jersey",
            'JM' => "Jamaica",
            'JO' => "Jordan",
            'JP' => "Japan",
            'KE' => "Kenya",
            'KG' => "Kyrgyzstan",
            'KH' => "Cambodia",
            'KI' => "Kiribati",
            'KM' => "Comoros",
            'KN' => "Saint Kitts and Nevis",
            'KP' => "Korea, Democratic People's Republic of",
            'KR' => "Korea, Republic of",
            'KW' => "Kuwait",
            'KY' => "Cayman Islands",
            'KZ' => "Kazakhstan",
            'LA' => "Lao People's Democratic Republic",
            'LB' => "Lebanon",
            'LC' => "Saint Lucia",
            'LI' => "Liechtenstein",
            'LK' => "Sri Lanka",
            'LR' => "Liberia",
            'LS' => "Lesotho",
            'LT' => "Lithuania",
            'LU' => "Luxembourg",
            'LV' => "Latvia",
            'LY' => "Libyan Arab Jamahiriya",
            'MA' => "Morocco",
            'MC' => "Monaco",
            'MD' => "Moldova, Republic of",
            'ME' => "Montenegro",
            'MG' => "Madagascar",
            'MH' => "Marshall Islands",
            'MK' => "Macedonia",
            'ML' => "Mali",
            'MM' => "Myanmar",
            'MN' => "Mongolia",
            'MO' => "Macao",
            'MP' => "Northern Mariana Islands",
            'MQ' => "Martinique",
            'MR' => "Mauritania",
            'MS' => "Montserrat",
            'MT' => "Malta",
            'MU' => "Mauritius",
            'MV' => "Maldives",
            'MW' => "Malawi",
            'MX' => "Mexico",
            'MY' => "Malaysia",
            'MZ' => "Mozambique",
            'NA' => "Namibia",
            'NC' => "New Caledonia",
            'NE' => "Niger",
            'NF' => "Norfolk Island",
            'NG' => "Nigeria",
            'NI' => "Nicaragua",
            'NL' => "Netherlands",
            'NO' => "Norway",
            'NP' => "Nepal",
            'NR' => "Nauru",
            'NU' => "Niue",
            'NZ' => "New Zealand",
            'OM' => "Oman",
            'PA' => "Panama",
            'PE' => "Peru",
            'PF' => "French Polynesia",
            'PG' => "Papua New Guinea",
            'PH' => "Philippines",
            'PK' => "Pakistan",
            'PL' => "Poland",
            'PM' => "Saint Pierre and Miquelon",
            'PN' => "Pitcairn",
            'PR' => "Puerto Rico",
            'PS' => "Palestinian Territory",
            'PT' => "Portugal",
            'PW' => "Palau",
            'PY' => "Paraguay",
            'QA' => "Qatar",
            'RE' => "Reunion",
            'RO' => "Romania",
            'RS' => "Serbia",
            'RU' => "Russian Federation",
            'RW' => "Rwanda",
            'SA' => "Saudi Arabia",
            'SB' => "Solomon Islands",
            'SC' => "Seychelles",
            'SD' => "Sudan",
            'SE' => "Sweden",
            'SG' => "Singapore",
            'SH' => "Saint Helena",
            'SI' => "Slovenia",
            'SJ' => "Svalbard and Jan Mayen",
            'SK' => "Slovakia",
            'SL' => "Sierra Leone",
            'SM' => "San Marino",
            'SN' => "Senegal",
            'SO' => "Somalia",
            'SR' => "Suriname",
            'ST' => "Sao Tome and Principe",
            'SV' => "El Salvador",
            'SY' => "Syrian Arab Republic",
            'SZ' => "Swaziland",
            'TC' => "Turks and Caicos Islands",
            'TD' => "Chad",
            'TF' => "French Southern Territories",
            'TG' => "Togo",
            'TH' => "Thailand",
            'TJ' => "Tajikistan",
            'TK' => "Tokelau",
            'TL' => "Timor-Leste",
            'TM' => "Turkmenistan",
            'TN' => "Tunisia",
            'TO' => "Tonga",
            'TR' => "Turkey",
            'TT' => "Trinidad and Tobago",
            'TV' => "Tuvalu",
            'TW' => "Taiwan",
            'TZ' => "Tanzania, United Republic of",
            'UA' => "Ukraine",
            'UG' => "Uganda",
            'UM' => "United States Minor Outlying Islands",
            'US' => "United States",
            'UY' => "Uruguay",
            'UZ' => "Uzbekistan",
            'VA' => "Holy See (Vatican City State)",
            'VC' => "Saint Vincent and the Grenadines",
            'VE' => "Venezuela",
            'VG' => "Virgin Islands, British",
            'VI' => "Virgin Islands, U.S.",
            'VN' => "Vietnam",
            'VU' => "Vanuatu",
            'WF' => "Wallis and Futuna",
            'WS' => "Samoa",
            'YE' => "Yemen",
            'YT' => "Mayotte",
            'ZA' => "South Africa",
            'ZM' => "Zambia",
            'ZW' => "Zimbabwe",
        );

        // Sort the list.
        natcasesort($countries);

        return $countries;
}


/*******************************************************************************
*  API Functions
*******************************************************************************/

/**
  * Function to test connection to the TeachersTV API settings
  * on the settings form.
  */
function teacherstv_test_connection($apiurl, $apiuser, $apipass) {

  // Set up the CoreAPI object with user-supplied values.
  $coreapi = new coreapi();
  $coreapi->apiuser = $apiuser;
  $coreapi->apipwd = $apipass;
  $coreapi->apiurl = $apiurl;

  // call the test_connection function in the CoreAPI and return the result if unsuccessful.
  $result = $result = $coreapi->test_connection();

  // coreAPI returns TRUE if a connection is made, otherwise it returns an int with the http result code.
  if (!is_int($result) && $result === TRUE) {
    return true;
  } else {
    return $result;
  }

  return false;
}

/**
 * Queries the API for a list of videos
 *
 * @param $category int
 *   Category to query the API against. Required.
 *
 * @return mixed
 *   Array of objects describing the vidoes
 */
function teacherstv_list_videos_in_api($category) {

  if (!is_array($category)) {

    $coreapi = new coreapi();
    $coreapi->apiuser = variable_get('teacherstv_apiusername', '');
    $coreapi->apipwd = teacherstv_get_password();
    $coreapi->apiurl = variable_get('teacherstv_apiurl', '');

    // Set the category ID
    $coreapi->category_id = $category;


    // Get a list of videos
          $coreapi->get_category_videos();

    if (isset($coreapi->video_list_details)) {
      return $coreapi->video_list_details;
    } else {
      return FALSE;
    }
  } else {
    return FALSE;
  }
}


/**
 * Grab video details from the Core Catalyst API
 *
 * @param $video_id int
 *   ID for the video file.
 *
 * @return object
 *   Returns video details as an object.
 */
function teacherstv_get_video_from_api($video_id) {
  $coreapi = new coreapi();
  $coreapi->apiuser = variable_get('teacherstv_apiusername', '');
  $coreapi->apipwd = teacherstv_get_password();
  $coreapi->apiurl = variable_get('teacherstv_apiurl', '');

  $coreapi->video_id = $video_id;
  $coreapi->get_info();
  return $coreapi;
}




/**
 * Get a list of categories from the database and eturns an array of categories,
 * in the following format (this matches the arrays created by the CoreAPI
 * library's get_catgories() method):
 *
 *   array(5) {
 *     [24]=>
 *     string(7) "Science"
 *     [25]=>
 *     string(5) "Music"
 *     [26]=>
 *     string(7) "Nursing"
 *     [27]=>
 *     string(10) "Psychology"
 *     [28]=>
 *     string(10) "Physics204"
 *   }
 *
 * @return array
 *   Returns an array of categories, in the above format.
 */
function teacherstv_get_categories_from_db() {
  $result = db_select('teacherstv_category','tc')
    ->fields('tc',array('id','category'))
    ->orderBy('category','asc')
    ->execute();


  $category_list = array();

  while ($category = $result->fetchAssoc()) {
    $category_list[$category['id']] = $category['category'];
  }


  if (sizeof($category_list) > 0) {
    return $category_list;
  } else {
    return FALSE;
  }
}

/**
 * Get a list of categories from the API
 *
 * @return array
 *   Returns an array of categories and IDs
 */
function teacherstv_get_categories_from_api() {

  $coreapi = new coreapi();
  $coreapi->apiuser = variable_get('teacherstv_apiusername', '');
  $coreapi->apipwd = teacherstv_get_password();
  $coreapi->apiurl = variable_get('teacherstv_apiurl', '');
  $coreapi->sortcriteria = "alpha";

  if ($coreapi->apipwd == "") {
    drupal_set_message(t('API password is not set.  Cannot connect to TeachersTV service.'),'error');
    return FALSE;
  }

  $coreapi->get_categories();

  return $coreapi->category_list;

}

/**
 * checks status of video in database against API. This should only be used
 * in the context of cron, as it can cause excessive load times when called
 * against a number of videos.
 *
 * This function also updates the database contents based on the contents of
 * the API for critical attributes (video_url, access_key, etc)
 *
 * If you're looking to query the status of a video, use the
 * teacherstv_get_video($video_id) function instead; this will
 * set the video_status attribute on the returned object.
 *
 * @param $video object
 *   Object of video details from the database.
 *
 * @return mixed
 *   Returns FALSE if video_id is not in database.
 *   Returns TRUE if video is updated.
 *   Returns -1 if no update is required.
 */
function teacherstv_check_video_status_in_api($video_id = NULL) {

  // Get current record from database.
  $db_video_details = teacherstv_get_video_from_db($video_id);

  // Get current record from API.
  $api_detail = teacherstv_get_video_from_api($video_id);

  if ($api_detail === FALSE) {
    // Video has been deleted.
    //!TODO: Add in call to teacherstv_delete_video_from_db)
    teacherstv_delete_video_from_db($video_id);
   return FALSE;
  }

  // Let's see if the API record has been modified and do further checks if it has.
  $output = -1;

    // Compare the two objects for the things that matter:
    //  Access_key.
    if ($api_detail->access_key <> $db_video_details->access_key) {
      // Update the field
      db_query("update {teacherstv_video} SET access_key='%s' WHERE id=%d", $api_detail->access_key, $video_id);
      $output = TRUE;
    }

    //  Video_status.
    if ($api_detail->video_status <> $db_video_details->video_status) {
      // Update the field
      db_query("update {teacherstv_video} SET video_status='%s' WHERE id=%d", $api_detail->video_status, $video_id);
      $output = TRUE;
    }

    //  Status.
    if ($api_detail->status <> $db_video_details->status) {
      // Update the field
      db_query("update {teacherstv_video} SET status='%s' WHERE id=%d", $api_detail->status, $video_id);
      $output = TRUE;
    }

    //  Video_url.
    if ($api_detail->video_url <> $db_video_details->video_url) {
      // Update the field
      db_query("update {teacherstv_video} SET video_url='%s' WHERE id=%d", $api_detail->video_url, $video_id);
      $output = TRUE;
    }

    //  Screenshot.
    if ($api_detail->screenshot <> $db_video_details->screenshot) {
      // Update the field
      db_query("update {teacherstv_video} SET screenshot='%s' WHERE id=%d", $api_detail->screenshot, $video_id);
      $output = TRUE;
    }

    // Embed.
    if ($api_detail->embed <> $db_video_details->embed) {
      // Update the field
      db_query("update {teacherstv_video} SET embed='%s' WHERE id=%d", $api_detail->embed, $video_id);
      $output = TRUE;
    }

    return $output;
}

/**
  * We have to store the API password in a way that is both secure, and
  * accessible to the module.  We're making use of either the mcrypt
  * library in PHP5, or the OpenSSL library, and store the password
  * using aes128 encryption, with a key specified in the module's
  * configuration.
  *
  * The function takes care of storing the password in the variable
  * teacherstv_apipassword and returns TRUE if successfully encrypted.
  *
  * @param $password string
  *   The password to encrypt, in cleartext.
  *
  * @return BOOL
  *   Returns TRUE if password is encrypted and saved, FALSE otherwise.
  */
function teacherstv_store_password($password) {
  $key = variable_get('teacherstv_cryptkey','');

  // Try mcrypt first, then fall back to OpenSSL
  if (function_exists('mcrypt_encrypt')) {
    $ciphertext = mcrypt_encrypt($password, 'aes128', $key);
  } else if (function_exists('openssl_encrypt')) {
    $iv = variable_get('teacherstv_iv',FALSE);
    if (!$iv) {
      $iv_len = openssl_cipher_iv_length('aes128');
      $iv = openssl_random_pseudo_bytes($iv_len);
      variable_set('teacherstv_iv', $iv);
    }
    $ciphertext = openssl_encrypt($password, 'aes128' , $key, 0, $iv);
  } else {
    // No crypt lib found. Send an error message back
    drupal_set_message(t('No crypto libraries found. Ensure your server has either MCrypt or OpenSSL installed'), 'error');
    return FALSE;
  }

  variable_set('teacherstv_apipassword', $ciphertext);
  return TRUE;
}


/**
 * Returns the unecrypted API password from the database
 * @param none
 * @return string
 *   Password stored in the database, now in cleartext.
 */
function teacherstv_get_password() {

  $key = variable_get('teacherstv_cryptkey','');

  $ciphertext = variable_get('teacherstv_apipassword');

  if ($ciphertext == '') {
    return '';
  }
  // Try mcrypt first, then fall back to OpenSSL
  if (function_exists('mcrypt_decrypt')) {
    $password = mcrypt_decrypt($ciphertext, 'aes128', $key);
  } else if (function_exists('openssl_decrypt')) {
    $iv = variable_get('teacherstv_iv',FALSE);
    $password = openssl_decrypt($ciphertext, 'aes128' , $key, 0, $iv);
  } else {
    // No crypt lib found. Send an error message back
    drupal_set_message(t('No crypto libraries found. Ensure your server has either MCrypt or OpenSSL installed'), 'error');
    return FALSE;
  }

    return $password;
}



/*******************************************************************************
*  Database Functions
*******************************************************************************/

/**
 * List video IDs stored in database.  This function is used both in the
 * admin/field pages, as well as by the cron job.  The more common usage of
 * this function would be to pull a list of videos for a specific category,
 * to be displayed to the user.  But it makes sense to also use this function
 * for the cron job when it goes to update videos from the API.
 *
 * @param $category int
 *   Integer ID of the category to check (optional)
 *
 * @return mixed
 *   Returns an array of objects containing video ID and title.
 */
function teacherstv_list_video_ids_in_db($category = NULL) {


// !TODO - rewrite queries to use db_select instead of db_query.
  if (!is_array($category)) {
    if (!is_null($category)) {
      $q = "SELECT {teacherstv_video}.id, {teacherstv_video}.title from {teacherstv_category_videos}, {teacherstv_video} where {teacherstv_category_videos}.categoryid = $category AND {teacherstv_video}.id = {teacherstv_category_videos}.videoid";
    }
    else {
      $q = "SELECT {teacherstv_video}.id, {teacherstv_video}.title,{teacherstv_video}.video_id from {teacherstv_video}";
    }
    $results = db_query($q);
    $videolist = array();

    // we have to format the data to match what the CoreAPI provides.
    while ($videos = $results->fetchAssoc()) {
      $videolist[$videos['id']] = $videos['title'];
    }

    if (sizeof($videolist) > 0) {
      return $videolist;
    } else {
      return FALSE;
    }
  } else {
    return FALSE;
  }
}


/**
 * Grab video details from the database.
 *
 * @param $video_id int
 *   ID for the video file.
 *
 * @return object
 *   Returns video details as an object.
 */
function teacherstv_get_video_from_db($video_id) {

  $video = db_select('teacherstv_video','tv')
    ->fields('tv')
    ->condition('id',$video_id,'=')
    ->execute()
    ->fetchObject();

  if (isset($video->id)) {
    return $video;
  } else {
    return false;
  }
}


/**
 * Stores a new video object in the database.
 *
 * @param $videos object
 *   Array containing the video's details.
 *
 * @return bool
 *   Returns success of saving the video
 */
function teacherstv_add_video_to_db($videolist, $categoryid = NULL) {

  if (is_object($videolist)) {
    $videos = array($videolist);
  } else {
    $videos = $videolist;
  }

  if (is_array($videos)) {

    foreach ($videos as $vkey => $video) {


    if (isset($video->title)) {
        $corevideo = new coreapi();
        $corevideo->video_id = $video->video_id;
        $screenshot = $corevideo->get_screenshot();

        $videoid = htmlentities($video->video_id);
        $title = htmlentities($video->title);
        $video_url = htmlentities($video->video_url);
        $published_date = date("U",strtotime($video->published_date));
        $created = date("U",strtotime($video->created));
        $modified = date("U",strtotime($video->modified));
        $status = htmlentities($video->status);
        $video_status = htmlentities($video->video_status);
        $downlod = htmlentities($video->download);
        $embed = htmlentities($video->embed);
        $description = htmlentities($video->description);
        $screenshot = htmlentities($screenshot);
        $access_key = htmlentities($video->access_key);


        /**
          * We're looping through an array of videos, so it's possible that
          * we'll try to add a video that already exists in the database. In
          * order to avoid database insert errors, let's check to see if the
          * video already exists and just skip to the next iteration if it does.
          */

        if ($existing_video = teacherstv_get_video_from_db($videoid)) {
          continue;
        }

        // If it doesn't, add the video.
        db_insert('teacherstv_video')
          ->fields(array(
            'id'=>$videoid,
            'video_id'=>$videoid,
            'title'=>$title,
            'video_url'=>$video_url,
            'published_date'=>$published_date,
            'created'=>$created,
            'modified'=>$modified,
            'status'=>$status,
            'video_status'=>$video_status,
            'download'=>$download,
            'embed'=>$embed,
            'description'=>$description,
            'screenshot'=>$screenshot,
            'access_key'=>$access_key,
            'cron_lastupdate'=>date("U"),
          ))
          ->execute();

  // Breaking this out to avoid duplicate video entries.
        // Now, associate the video with the category, but only if $categoryid is set.
        if ($categoryid <> NULL) {
          teacherstv_add_video_to_category($video->video_id, $categoryid);
        }
      }
    }
  }
}



/**
 * Associates a video ID with a category
 *
 * @param $vidid int
 *   ID of the video
 * @param $categoryid int
 *   ID of the category
 *
 * @return bool
 */
function teacherstv_add_video_to_category($vidid,$categoryid) {

  $listing = db_select('teacherstv_category_videos','tcv')
                ->fields('tcv')
                ->condition('categoryid',$categoryid)
                ->condition('videoid',$vidid)
                ->execute()
                ->fetchAssoc();

  if ($listing === FALSE) {
    db_insert('teacherstv_category_videos')
      ->fields(array(
        'categoryid'=>$categoryid,
        'videoid'=>$vidid
      ))
      ->execute();
    return true;
  } else {
    return false;
  }
}


/**
 * Stores new category or categories in the database.
 *
 * @param $category array
 *   Array containing category details.
 *
 * @return bool
 *   Returns success of saving the category.
 */
function teacherstv_add_category_to_db($categories) {
  foreach ($categories as $cid => $category) {
    db_insert('teacherstv_category')
      ->fields(array(
        'id'=>$cid,
        'category'=>htmlentities($category),
        ))
      ->execute();
  }

}

/**
 * Deletes a category from the database listing
 *
 * @param $categoryid int
 *   ID of the category
 *
 * @return bool
 *   Returns success of operation
 */
function teacherstv_delete_category_from_db($categoryid) {

  // Remove the category.
  if ($db_query("DELETE FROM {teacherstv_category} WHERE {teacherstv_category}.id = %d", $categoryid)) {

    // Now remove the videos associated with that category to
    // maintain database integrity.
    db_query("DELETE FROM {teacherstv_category_videos} WHERE {teacherstv_category_videos}.categoryid = %d", $categoryid);
  } else {
    return FALSE;
  }
}

/**
 * Deletes a video from the database listing
 *
 * @param $categoryid int
 *   ID of the category
 *
 * @return bool
 *   Returns success of operation
 */
function teacherstv_delete_video_from_db($videoid) {

  // Delete the video.
  if (db_query("DELETE FROM {teacherstv_video} WHERE {teacherstv_video}.id = %d", $videoid)) {

    // Delete the video from the category listing to maintain database integrity.
    db_query("DELETE FROM {teacherstv_category_videos} WHERE {teacherstv_category_videos}.videoid = %d", $videoid);
    return TRUE;
  } else {
    return FALSE;
  }
}

/**
 * Updates the database values based on data from Core API
 *
 * @param $video_id int
 *   Core API video_id
 * @param $field string
 *   Field to update
 * @param $value string
 *   Value to set for field.
 *
 * @return bool
 *   Return successful/failed operation.
 */
function teacherstv_update_db_record($uid, $field, $value) {

  switch ($field) {
    case 'title':
    case 'video_url':
    case 'screenshot':
    case 'description':
    case 'published_date':
    case 'created':
    case 'modified':
    case 'status':
    case 'video_status':
    case 'download':
    case 'embed':
    case 'access_key':
    case 'cron_lastupdate':
      $db_field = array($field);      
//       if ($result = db_query("update {teacherstv_video} SET :field = :value where video_id = :videoid", array(':field' => $field, ':value' => (string)  htmlentities($value), ':videoid' => $uid))) {
      if ($result = db_update('teacherstv_video')
        ->fields(array($field=>htmlentities($value)))
        ->condition('video_id',$uid,'=')
        ->execute()) {
        return TRUE;
      } else {
        return FALSE;
      }
      break;
    case 'category_videos':
//       if ($result = db_query("UPDATE {teacherstv_category_videos} SET categoryid=:categoryid WHERE videoid=:videoid",array(':categoryid'=>$value,':videoid'=>$uid))) {
      if ($result = db_update('teacherstv_category_videos')
        ->fields(array('categoryid'=>$value))
        ->condition('video_id',$value,'=')
        ->execute()) {
        return TRUE;
      } else {
        return FALSE;
      }
      break;

    case 'category':
//       if (db_query("UPDATE {teacherstv_category} SET category=:category WHERE id=:id", array(':category'=>$value,':id'=>$uid))) {
      if ($result = db_update('teacherstv_category')
        ->fields(array('category'=>$value))
        ->condition('id',$uid,'=')
        ->execute()) {
        return TRUE;
      } else {
        return FALSE;
      }
      break;
    // Catch-all for things we're not expecting.
    default:
      return false;
      break;
  }
}


/**
 * Helper function for teacherstv_cron()
 *
 * @return bool
 */
function teacherstv_check_db_status() {

  //!TODO: Finish this function
  // update the category list
  teacherstv_check_category_list();


  // update the list of videos for each category;
    $categories = teacherstv_get_categories();
    foreach ($categories as $catid => $category) {
      teacherstv_check_category_video_list($catid);
    }


  $lastrun = variable_get('teacherstv_cron_lastrun', time());

  $time = date("U");

  if ($lastrun <> FALSE) {
    $elapsed = $time - $lastrun;
  }

  // run this only every two hours to keep requests to the API respectful.
  if ($elapsed >= 7200) {
    // Check status of stored videos
    $videos = teacherstv_list_videos_in_db();
    foreach ($videos as $videoid => $video) {
      // Check the status of each video.
      $status = teacherstv_check_video_status_in_api($videoid);

      // Look for errors.
      if ($status <> TRUE) {

      }
    }
    variable_set('teacherstv_cron_lastrun', $time);
  }

}

/*******************************************************************************
* Generic data Functions
*******************************************************************************/


/**
 * Wrapper functionn for grabbing video details.
 *
 * @param $video_id int
 *   ID for the video file.
 *
 * @return object
 *   Returns video details as an object.
 */

function teacherstv_get_video($video_id) {

    $details = teacherstv_get_video_from_db($video_id);

    if (!$details = teacherstv_get_video_from_db($video_id)) {
      $details = teacherstv_get_video_from_api($video_id);
      teacherstv_add_video_to_db($details);
    }
    return $details;
}

/** Abstract function to get categories
 *
 * @return array
 *   Returns array of categories.
 */
function teacherstv_get_categories() {

  // Check database for list of categories.
  if (!$categories = teacherstv_get_categories_from_db()) {
    // If no categories are found, we'll have to grab them from the API and
    // store them in the database now.
    $categories = teacherstv_get_categories_from_api();
    teacherstv_add_category_to_db($categories);
  }

  return $categories;

}

/**
 * Return associated categor(ies) for a video
 *
 * @param $videoid int
 *   Video_id of a video
 *
 * @return array
 *   Array of categories assigned to a video
 */
function teacherstv_get_category_for_video($videoid) {

  $result = db_query("SELECT {teacherstv_category_videos}.categoryid, {teacherstv_category}.category FROM {teacherstv_category_videos}, {teacherstv_category} WHERE {teacherstv_category_videos}.videoid = %d", $videoid);

  return db_fetch_array($result);

}

/**
 * Abstract function for listing videos.
 *
 * @param $category int
 *   Integer ID of the category to check (optional)
 *
 * @return mixed
 *   Returns an array of objects containing video ID and title.
 */
function teacherstv_list_videos($category = NULL) {
    if (is_null($category) || $category == 0) {
      return FALSE;
    }

    if (!$videos = teacherstv_list_videos_in_db($category)) {
      $videos = teacherstv_list_videos_in_api($category);
      teacherstv_add_video_to_db($videos, $category);

      $videolist = array();

//      return $videos;
       if (is_array($videos)) {
        foreach ($videos as $video) {
          $video_id = (array)$video->video_id;
          $videolist[$video->video_id] = $video->title . " (ID: " . $video->video_id . ")";
        }
      asort($videolist);
      }
      $videos = $videolist;
    }


/*
    $videos = array(
    NULL=>'Select a video',
    1=>'Video 1',
    2=>'Video 2',
    3=>'Video 3',
    4=>'Video 4',
  );
*/


    return $videos;
}


/**
 * Queries the API for a list of video IDs in a specific category
 *
 * @param $category int
 *   ID of the category to list
 *
 * @return mixed
 *   Array of video IDs
 */

function teacherstv_list_video_ids_in_api($category) {

  $coreapi = new coreapi();
  $coreapi->apiuser = variable_get('teacherstv_apiusername', '');
  $coreapi->apipwd = teacherstv_get_password();
  $coreapi->apiurl = variable_get('teacherstv_apiurl', '');

  // Set the category ID
  $coreapi->category_id = $category;


  $coreapi->get_category_video_ids();

  return $coreapi->video_id_list;

}

/**
 * List videos stored in database.  This function is used both in the
 * admin/field pages, as well as by the cron job.  The more common usage of
 * this function would be to pull a list of videos for a specific category,
 * to be displayed to the user.  But it makes sense to also use this function
 * for the cron job when it goes to update videos from the API.
 *
 * @param $category int
 *   Integer ID of the category to check (optional)
 *
 * @return mixed
 *   Returns an array of objects containing video ID and title.
 */
function teacherstv_list_videos_in_db($category = NULL) {

  if (!is_array($category)) {
    if (!is_null($category)) {

      $query = db_select('teacherstv_video','v');
      $query->leftjoin('teacherstv_category_videos','cv', 'v.id = cv.videoid');
      $query->fields('v',array('video_id','title','id'));
      $query->condition('cv.categoryid',$category,'=');
      $results = $query->execute();
    }
    else {
      $results = db_select('teacherstv_video','v')
                  ->fields('v',array('id','title','video_id'))
                  ->execute()
                  ->fetchAll();
    }

    $videolist = array();

    // we have to format the data to match what the CoreAPI provides.
    foreach ($results as $videos) {
      $videolist[$videos->id] = ucfirst($videos->title) . " (ID: {$videos->id})";
    }

    // array_multisort(array_map('strtolower', $videolist), $videolist);
    asort($videolist);


    if (sizeof($videolist) > 0) {
      return $videolist;
    } else {
      return FALSE;
    }
  } else {
    return FALSE;
  }
}


/**
 * Helper function for teacherstv_check_db_status. Gets updated list of categories from
 *
 * @param none
 * @return none
 */
function teacherstv_check_category_list() {
  // Check list of categories. Update if changed.
  $db_categories = teacherstv_get_categories_from_db();
  $api_categories = teacherstv_get_categories_from_api();
  
  // Compare the two lists of categories. We should use the API as the
  // canonical source.

  foreach ($api_categories as $apikey => $apicat) {
    // check to see if the category exists by ID
    if (array_key_exists($apikey, $db_categories)) {
      if ($apicat <> $db_categories[$apikey]) {
        teacherstv_update_db_record($apikey, 'category', $apicat);
      }
    } else {
      // category does not exist; add it
      teacherstv_add_category_to_db(array($apikey => $apicat));
    }
  }
}


/**
 * Helper function for teacherstv_check_db_status. Gets updated list of categories from
 *
 * @param $categoryid int
 *   ID of the category to check.
 * @return bool
 *   Returns TRUE if no additional videos found, FALSE if list is modified.
 */
function teacherstv_check_category_video_list($categoryid) {
//  $api_vids = teacherstv_list_videos_in_api($categoryid);
  $api_vids = teacherstv_list_video_ids_in_api($categoryid);

  $db_vids = teacherstv_list_video_ids_in_db($categoryid);

  // build a queue for updating
  $vidqueue = array();


  if (is_array($api_vids) && is_array($db_vids)) {

      foreach ($api_vids as $apikey) {

        // check to see if video exists in the DB already
        if (!array_key_exists($apikey, $db_vids)) {
          $vidqueue[] = $apikey;
        }
      }

      // Do we have videos to add?  Let's add them!
      if (sizeof($vidqueue) > 0) {
      // Add queued videos
          // get details about the video
          $coreapi = new coreapi();
          $coreapi->apiuser = variable_get('teacherstv_apiusername', '');
          $coreapi->apipwd = teacherstv_get_password();
          $coreapi->apiurl = variable_get('teacherstv_apiurl', '');

          $coreapi->video_id = $apikey;
          $coreapi->video_id_list = $vidqueue;
          $foo = $coreapi->get_info_multi();

          foreach($coreapi->video_list_details as $key=>$val) {

            // first, see if the video record already exists in DB

            $dbrecord = teacherstv_get_video_from_db($key);

            if ($dbrecord === FALSE) {
              teacherstv_add_video_to_db(array($val), $categoryid);
            } else {

              teacherstv_add_video_to_category($dbrecord->id, $categoryid);
            }
          }
      }
  }
}




/*******************************************************************************
*  Cron Functions
*******************************************************************************/


function teacherstv_cron_check_catvids() {

  // update the list of videos for each category;
    $categories = teacherstv_get_categories();
    foreach ($categories as $catid => $category) {
      teacherstv_check_category_video_list($catid);
    }
}

function teacherstv_cron_check_video_list() {
  // Check status of stored videos
  $videos = teacherstv_list_videos_in_db();

  foreach ($videos as $videoid => $video) {

    // check last updated time;

    $currenttime = date("U");
    $lastupdate = teacherstv_cron_check_last_update($videoid);
    $elapsed = (int)$currenttime - (int)$lastupdate;

    // only check video status every 2 hours.
    // TODO: Make this configurable.
    if ($lastupdate < $currenttime - 7200) {

      // Get details from the API and the DB to compare.
      $api_video = teacherstv_get_video_from_api($videoid);
      $db_video = teacherstv_get_video_from_db($videoid);

      /** 
       * There must be a smarter way to do this update; this has the potential
       * to hit the DB more than it needs to.
       *
       * Perhaps bundle up all the changes into a single query?
       */

      if ($api_video->title <> $db_video->title) {
        teacherstv_update_db_record($videoid, 'title', $api_video->title);
      }
      if ($api_video->video_url <> $db_video->video_url) {
        teacherstv_update_db_record($videoid, 'video_url', $api_video->video_url);
      }
      if ($api_video->screenshot <> $db_video->screenshot) {
        teacherstv_update_db_record($videoid, 'screenshot', $api_video->screenshot);
      }
      if ($api_video->description <> $db_video->description) {
        teacherstv_update_db_record($videoid, 'description', $api_video->description);
      }
      if ($api_video->status <> $db_video->status) {
        teacherstv_update_db_record($videoid, 'status', $api_video->status);
      }
      if ($api_video->video_status <> $db_video->video_status) {
        teacherstv_update_db_record($videoid, 'video_status', $api_video->video_status);
      }
      if ($api_video->download <> $db_video->download) {
        teacherstv_update_db_record($videoid, 'download', $api_video->download);
      }
      if ($api_video->embed <> $db_video->embed) {
        teacherstv_update_db_record($videoid, 'embed', $api_video->embed);
      }
      if ($api_video->access_key <> $db_video->access_key) {
        teacherstv_update_db_record($videoid, 'access_key', $api_video->access_key);
      }
      teacherstv_update_db_record($videoid, 'cron_lastupdate', date("U"));
    }
  }
}

/**
 * Returns unix timestamp for last update.  This is currently only used for 
 * the teacherstv_video table. However, in the future, we may want to add the
 * cron_lastupdate column to the other tables to reduce hits on the API.
 *
 * @param $id int
 *   ID of item to check
 *
 * @return int
 *   Returns Unix timestamp as integer.
 */
function teacherstv_cron_check_last_update($id, $table="teacherstv_video") {

  $query = db_select($table,'tv')
    ->fields('tv',array('cron_lastupdate','id'))
    ->condition('tv.id',$id,'=')
    ->execute();

  $result = $query->fetchAssoc();
  
  return $result['cron_lastupdate'];
}





/**
 * implementation of hook_cron()
 * Runs scheduled tasks for database maintenance
 *
 */
function teacherstv_cron() {

  // check the category lists.
  teacherstv_cron_check_catvids();

  // check the individual video records
  teacherstv_cron_check_video_list();

  // check the list of categories
  teacherstv_check_category_list();


/*
    // check database status of videos.
    teacherstv_check_db_status();
*/
}
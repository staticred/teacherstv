<?php
/**
 * @file 
 * Contains the basic 'teacherstv' field handler. This sets up all of the 
 * options Views needs to render the video. 
 */

error_log("Loading teacherstv_field_views_video_handler.inc.");

class teacherstv_field_views_video_handler extends views_handler_field {


  function construct() {
    parent::construct();
//    $this->additional_fields['nid'] = 'nid';
    error_log("Constructed object.");
  }

  /**
   * 
   * set up options
   */
  function option_definition() {
    $options = parent::option_definition();

    $options['display'] = array('default' => 'video');
    $options['video_settings']['width'] = array('default' => '');
    $options['video_settings']['height'] = array('default' => '');
    $options['video_settings']['enforce_roles'] = array('default' => '');
    $options['video_settings']['enforce_geo'] = array('default' => '');
    $options['video_settings']['hide_null'] = array('default' => '');
    $options['video_settings']['hide_errors'] = array('default' => '');
    $options['screenshot_settings']['width'] = array('default' => '');
    $options['screenshot_settings']['height'] = array('default' => '');
    $options['screenshot_settings']['maintain_ratio'] = array('default' => '');
    $options['screenshot_settings']['link_to_node'] = array('default' => '');
    
    return $options;
  }

  
  
  /**
   * Provide width/height options
   */
 
  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);

    $form['display'] = array(
      '#title' => t('Type of display'),
      '#type' => 'radios',
      '#options' => array('video' => t('Video'), 'screenshot' => t('Screenshot')),
      '#default_value' => $this->options['display'],
      '#attributes' => array('onchange' => "javascript:if (\$(this).val() == 'video') {\$('#teacherstv_video_settings').show('fast'); $('#teacherstv_screenshot_settings').hide('fast');}else {\$('#teacherstv_video_settings').hide('fast'); $('#teacherstv_screenshot_settings').show('fast');}")
    );

    $initialstyle = ($this->options['display'] == "video") ? "" : " style=\"display:none\"";
    $form['video_settings'] = array(
      '#type' => 'fieldset',
      '#title' => t('Video Settings'),
      '#prefix' => "<div id=\"teacherstv_video_settings\"{$initialstyle}>",
      '#suffix' => "</div>"
    );
    $form['video_settings']['width'] = array(
      '#title' => t('Width in px'),
      '#description' => t('This will override any default or previous value set. Leave blank to use defaults/preset'),
      '#type' => 'textfield',
      '#size' => 4,
      '#default_value' => $this->options['video_settings']['width'],
    );
    $form['video_settings']['height'] = array(
      '#title' => t('Height in px'),
      '#description' => t('This will override any default or previous value set. Leave blank to use defaults/preset'),
      '#type' => 'textfield',
      '#size' => 4,
      '#default_value' => $this->options['video_settings']['height'],
    );
    $form['video_settings']['enforce_roles'] = array(
      '#title' => t('Disable Role Restrictions'),
      '#description' => t('Check this to allow any role to view the video, leaving unchecked will enforce the role restrictions set on each video.  When in doubt, leave it unchecked.'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['video_settings']['enforce_roles'],
    );
    $form['video_settings']['enforce_geo'] = array(
      '#title' => t('Disable Country Restrictions'),
      '#description' => t('Check this to allow users from any country to view the video, leaving unchecked will enforce the geo restrictions set on each video.  When in doubt, leave it unchecked.'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['video_settings']['enforce_geo'],
    );
    $form['video_settings']['hide_null'] = array(
      '#title' => t('Hide Non-Existant Video Message'),
      '#description' => t('Check this to suppress the error message when a video does not exist. Other errors will be visible, if there are no other errors there will be no output.'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['video_settings']['hide_null'],
    );
    $form['video_settings']['hide_errors'] = array(
      '#title' => t('Hide Errors'),
      '#description' => t('Check this to suppress error messages relating to unavailable videos (encoding, etc), as well as messages relating to role/country permissions not being met.'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['video_settings']['hide_errors'],
    );
    
    $initialstyle = ($this->options['display'] == "screenshot") ? "" : " style=\"display:none\"";
    $form['screenshot_settings'] = array(
        '#type' => 'fieldset',
        '#title' => t('Screenshot Settings'),
        '#prefix' => "<div id=\"teacherstv_screenshot_settings\"{$initialstyle}>",
        '#suffix' => "</div>"
    );
    $form['screenshot_settings']['width'] = array(
          '#title' => t('Width in px'),
          '#description' => t('Set a width for the image - leave blank to use the full resolution of the image.'),
          '#type' => 'textfield',
          '#size' => 4,
          '#default_value' => $this->options['screenshot_settings']['width'],
    );
    $form['screenshot_settings']['height'] = array(
          '#title' => t('Height in px'),
          '#description' => t('Set a height for the image - leave blank to use the full resolution of the image.'),
          '#type' => 'textfield',
          '#size' => 4,
          '#default_value' => $this->options['screenshot_settings']['height'],
    );
    $form['screenshot_settings']['maintain_ratio'] = array(
        '#title' => t('Maintain Aspect Ratio'),
        '#description' => t('This will treat the given width/height as maximum values and fit the image into those dimensions without stretching it.'),
        '#type' => 'checkbox',
        '#default_value' => $this->options['screenshot_settings']['maintain_ratio'],
    );
    $form['screenshot_settings']['link_to_node'] = array(
        '#title' => t("Link the image to it's node"),
        '#type' => 'checkbox',
        '#default_value' => $this->options['screenshot_settings']['link_to_node'],
    );
  }
 
   /**
  * Loads additional fields.
  */
/*
  function query() {
    error_log("Entering query()");
    $this->ensure_my_table();
    $this->add_additional_fields();
  }
*/

  function render($values) {
    error_log("In render step.");
    global $base_url;
    
    
    // get our field aliases
    $aliases = array_keys($this->aliases);
    
    // now we have traceable fields
    $uid_field = $this->aliases[$aliases[0]];
    $title_field = $this->aliases[$aliases[1]];
    $description_field = $this->aliases[$aliases[2]];
    $category_field = $this->aliases[$aliases[3]];
    $width_field = $this->aliases[$aliases[4]];
    $height_field = $this->aliases[$aliases[5]];
    $georestrict_field = $this->aliases[$aliases[6]];
    $countryperms_field = $this->aliases[$aliases[7]];
    $rolerestrict_field = $this->aliases[$aliases[8]];
    $roleperms_field = $this->aliases[$aliases[9]];
    $h264_field = $this->aliases[$aliases[10]];
    $nid = $this->aliases[$aliases[11]];
    
    
    // return nothing if we don't have an id
    if (!is_numeric($values->$uid_field)) {
        return FALSE; // also saves additional processing
    }
    
    $width = (is_numeric($this->options['video_settings']['width']) && $this->options['video_settings']['width'] > 1) ? $this->options['video_settings']['width'] : FALSE;
    if (!$width) {
        $width = $values->$width_field;
    }
    
    $height = (is_numeric($this->options['video_settings']['height']) && $this->options['video_settings']['height'] > 1) ? $this->options['video_settings']['height'] : FALSE;
    if (!$height) {
        $height = $values->$height_field;
    }
    
    $countryperms = unserialize($values->$countryperms_field);
    $roleperms = unserialize($values->$roleperms_field);
    
/*
    $video = new coreapi();
    $video->apiuser = variable_get('teacherstv_username', '');
    $video->apipwd = teacherstv_get_password();
    $video->apiurl = variable_get('teacherstv_url', '');
    $video->video_id = $values->$uid_field;
    $video->embed_width = $width;
    $video->embed_height = $height;
    $status = $video->get_video_status();
*/
    
    $video = teacherstv_get_video($values->$uid_field);
    $status = $video->video_status;
    
    $errors = array();
    $output = TRUE;
        
    if ($this->options['display'] == "video") {
      if (empty($values->$uid_field)) {
          if (!$this->options['video_settings']['hide_null']) {
              $errors[] = t('Video does not exist.');
              $output = FALSE;
          }
      }
      elseif ($status != 5) {
        // the video is not available for embedding
        if (!$this->options['video_settings']['hide_errors']) {
            $errors[] = t('Sorry, we are experiencing technical difficulties with this video.  Please try viewing this video at a later time.');
            if (user_access('teacherstv_admin')) {
                $coreapi = new coreapi();
                $errors[] = t('Current status: @status', array('@status' => $coreapi->video_status_code[$status]));
                unset($coreapi);
            }
            $output = FALSE;
        }
      }
    
        // check geo permissions
      if ($values->$georestrict_field && !$this->options['video_settings']['enforce_geo']) {
        // we have to check the users location to make sure it's in the array of allowed values
        global $user;
            
        $geocheck = FALSE;
        if (is_array($countryperms) && count($countryperms) > 0) {
            if (in_array(geoip_country_code(), $countryperms)) {
                // actually, we are allowed
                $geocheck = TRUE;
            }
        }
            
        if (!$geocheck) {
            if (!$this->options['video_settings']['hide_errors']) {
                $errors[] = t('This video is not licensed for viewing in your country');
                $output = FALSE;
            }
        }
      }
    
        //   check role permissions
      if ($values->$rolerestrict_field && !$this->options['video_settings']['enforce_roles']) {
        // we have to check the users role permissions
        global $user;
            
        $rolecheck = FALSE;
            
        if (is_array($roleperms) && count($roleperms) > 0) {
            foreach ($roleperms as $allowed) {
                if (in_array($allowed, array_keys($user->roles))) {
                    // hey, we are allowed to see it!
                    $rolecheck = TRUE;
                }
            }
        }
            
        if (!$rolecheck) {
            if (!$this->options['video_settings']['hide_errors']) {
                if (user_is_logged_in()) {
                    $errors[] = t("Sorry, but you don't have the necessary site permissions to view this video.");
                }
                else {
                    $errors[] = t("AU Students can view this film by logging into the site using their single-sign-on credentials.");
                }
                $output = FALSE;
            }
        }
      }
    
    
      if (count($errors) > 0) {
        // we don't get to see the video
        // set any of our errors
        $html .= "<div class=\"teacherstv_error\">";
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
        if ($values->$h264_field && teacherstv_detect_mobile()) {
            $embed_type = 'mobile';
        }
        else {
            $embed_type = 'flash';
        }

        if ($output) {
            // $html .= "<div class=\"teacherstv_embed\">" . $video->embed() . "</div>";
            $html .= "<div class=\"teacherstv_embed\">" . teacherstv_embed($values->$uid_field, $embed_type, $width, $height) . "</div>";
        }
      }
    }
    else {
     // not displaying video - display screenshot
      drupal_set_message("entering screenshot render.");
      $style = "";
      if ($this->options['screenshot_settings']['width'] || $this->options['screenshot_settings']['height']) {
        // custom dimensions, create style
        $style = " style=\"";
        
        if (is_numeric($this->options['screenshot_settings']['width']) && $this->options['screenshot_settings']['width'] > 0) {
          $img_width = $this->options['screenshot_settings']['width'];
        }
        if (is_numeric($this->options['screenshot_settings']['height']) && $this->options['screenshot_settings']['height'] > 0) {
            $img_height = $this->options['screenshot_settings']['height'];
        }
        
        
        // keep the aspect ratio of the original image
        if ($this->options['screenshot_settings']['maintain_ratio']) {
            $imginfo = getimagesize($video->screenshot);
            
            if ($imginfo) {
                $realratio = $imginfo[0]/$imginfo[1];
                
                //set ratio greater than realratio by default in case $img_height = 0
                $setratio = $realratio + 1;
                if (!empty($img_height)) {
                    $setratio = $img_width/$img_height;
                }
                
                // set the largest dimension to "auto"
                if ($realratio > $setratio || empty($img_height)) {
                    // constrain the height
                    $img_height = "auto";
                }
                elseif ($realratio < $setratio || empty($img_width)) {
                    $img_width = "auto";
                }
                // if the ratio is perfect we don't need to change anything
            }
        }
        
        
        if ($img_width) {
            $style .= "width:" . $img_width;
            if ($img_width != "auto") {
                $style .= "px";
            }
            $style .= ";";
        }
        
        if ($img_height) {
            $style .= "height:" . $img_height;
            if ($img_height != "auto") {
                $style .= "px";
            }
            $style .= ";";
        }
        $style .= "\"";
      }
      
     //get node title for alt text
     $title = str_replace('"', '\"', $values->$title_field);
     
     $img = $video->screenshot ? "<img src=\"{$video->screenshot}\" alt=\"" . t('Screenshot of video: @title', array('@title' => $title)) . "\"{$style}>" : "";
     
     
     if ($this->options['screenshot_settings']['link_to_node'] && $img) {
        // link the image to the node
        $path = url("node/{$values->$nid}");
     
        $html .= l($img, $base_url . $path, array('html' => TRUE));
     }
     else {
        $html .= $img;
     }
     
    }
    
    return format_string($html);

  }

}
 

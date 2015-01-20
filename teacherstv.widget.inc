<?php

/**
  * @file functions for field widget
  */

/**
  * Build an options list for the Teacherstv field
  *
  * @param int $catid 
  *    The ID of the category to get videos for.
  *
  * @return string
  *    An HTML string containing a list of videos to select.
  */
function teacherstv_widget_form_get_videos($catid = NULL) {
  if (!isset($catid) || is_null($catid)) {
    return false;
  }
  
  $videos = teacherstv_list_videos($catid);
    
  $options = array(NULL => t('Select a video'));
  if (is_array($videos)) {
    foreach ($videos as $key=>$video) {
      $options[$key] = $video;    
    }
  }
  return $options;

}

/**
 * Gets a list of video categories to populate the widget.
 *
 * @param none
 *
 * @return array
 *   Returns an array of category IDs and names. 
 */
function teacherstv_widget_form_get_categories() {
  $categories = teacherstv_get_categories();
  $options = array(NULL => t('Select a video category'));
    
  if (is_array($categories)) {
    foreach ($categories as $key=>$val) {
      $options[$key] = $val;
    }
  } else {
    return FALSE;
  }

  return $options;

}

/**
 * AJAX callback function for the video selection form. Based on the category
 * selected in the form, a list of videos will be displayed in the appropriate
 * section. 
 */
function teacherstv_ajax_callback($form, $form_state) {

  $form_state['rebuild'] = TRUE;
  $parents = $form_state['triggering_element']['#array_parents'];
  array_pop($parents);
  $element = drupal_array_get_nested_value($form, $parents);
  
  return $element['videoid'];
}
<?php
/**
* @file
* Views definitions for teacherstv module.
*/
 
function teacherstv_views_handlers() {
  return array(
    'info' => array(
      'path' => drupal_get_path('module','teacherstv') . '/views',
    ),
    'handlers' => array(
      'teacherstv_field_views_video_handler' => array(
        'parent' => 'views_handler_field',
      ),
    ),
  );
}

/**
* Implements hook_views_data().
*/
function teacherstv_views_data() {
  $data = array();
  
  $data['teacherstv_video']['table']['group'] = t('TeachersTV Table');
  $data['teacherstv_video']['table']['base'] = array(
    'field' => 'id',
    'title' => t('TeachersTV'),
  );

  
  $data['teacherstv_video'] = array(
    'title' => t('TeachersTV video Field'),
    'help' => t('TeachersTV video.'),
    'field' => array(
      'handler' => 'teacherstv_field_views_video_handler',
    ),      
  );
  
  return $data;
}
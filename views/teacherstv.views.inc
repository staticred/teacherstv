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
  
  $data['teacherstv']['table']['group'] = t('TeachersTV Table');
  $data['teacherstv']['table']['base'] = array(
    'field' => 'nid',
    'title' => t('TeachersTV'),
  );
    
 $data['teacherstv']['table']['join'] = array(
    
    // Index this array by the table name to which this table refers.
    // 'left_field' is the primary key in the referenced table.
    // 'field' is the foreign key in this table.
    'node' => array(
      'left_field' => 'videoid',
      'field' => 'videoid',
    ),
  );
/*
  $data['teacherstv']['nid'] = array(
    'title' => t('Example content'),
    'help' => t('Some example content that references a node.'),
    
    // Define a relationship to the {node} table, so example_table views can
    // add a relationship to nodes. If you want to define a relationship the
    // other direction, use hook_views_data_alter(), or use the 'implicit' join
    // method described above.
    'relationship' => array(
      'base' => 'node', // The name of the table to join with.
      'base field' => 'nid', // The name of the field on the joined table.
      // 'field' => 'nid' -- see hook_views_data_alter(); not needed here.
      'handler' => 'views_handler_relationship',
      'label' => t('Default label for the relationship'),
      'title' => t('Title shown when adding the relationship'),
      'help' => t('More information on this relationship'),
    ),
  );
  
*/
  $data['teacherstv']['teacherstv_video'] = array(
    'title' => t('TeachersTV video Field'),
    'help' => t('TeachersTV video.'),
    'field' => array(
      'handler' => 'teacherstv_field_views_video_handler',
    ),      
  );
  
/*
  $data['teacherstv_video']['screenshot'] = array(
    'title' => t('TeachersTV Screenshot Field'),
    'help' => t('TeachersTV Screenshot.'),
    'field' => array(
      'handler' => 'teacherstv_field_views_screenshot_handler',
  ),
  );
*/
  return $data;
}
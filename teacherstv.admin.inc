<?php



/**
 * Form builder; Administration settings form.
 *
 * @see system_settings_form()
 * @see teacherstv_settings_form_validate()
 */
function teacherstv_settings_form($form, &$form_state) {

  $form['teacherstv_api'] = array(
    '#type' => 'fieldset',
    '#title' => t('Teachers TV API'),
    '#collapsible' => TRUE,
    '#weight' => 0,
  );

  $form['teacherstv_api']['teacherstv_cryptkey'] = array(
      '#type' => 'textfield',
      '#title' => t('API Salt'),
      '#default_value' => variable_get('teacherstv_cryptkey', substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 16)),
      '#size' => 24,
      '#description' => t('A salt for storing the API Password in the database. Must be 16 characters. <b>If you are unsure what this does, do not modify this setting.</b>'),
      '#required' => TRUE,
  );

  $form['teacherstv_api']['teacherstv_apiurl'] = array(
      '#type' => 'textfield',
      '#title' => t('API URL'),
      '#default_value' => variable_get('teacherstv_apiurl', 'http://au.teacherstv.ca/api/video/index'),
      '#description' => t('Set the URL for the video streaming API'),
      '#required' => TRUE,
  );


  $form['teacherstv_api']['teacherstv_apiusername'] = array(
    '#type' => 'textfield',
    '#title' => t('User name for TeachersTV API'),
    '#description' => t('User name used to access the TeachersTV API.'),
    '#default_value' => variable_get('teacherstv_apiusername', ''),
      '#required' => TRUE,
  );
  $password = teacherstv_get_password();
  $form['teacherstv_api']['teacherstv_apipassword'] = array(
    '#type' => 'textfield',
    '#title' => t('Password for TeachersTV API'),
    '#description' => t('Password used to access the TeachersTV API.  This password will be stored, encrypted, in the database.'),
    '#default_value' => $password,
      '#required' => TRUE,
  );

  $form['teacherstv_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Default video settings'),
    '#collapsible' => TRUE,
    '#weight' => 10,
  );

  $form['teacherstv_settings']['teacherstv_mobileready'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable mobile video detection'),
    '#default_value' => variable_get('teacherstv_mobileready',FALSE),
  );
  $form['teacherstv_settings']['teacherstv_videowidth'] = array(
    '#type' => 'textfield',
    '#title' => t('Default width (in pixels)'),
    '#default_value' => variable_get('teacherstv_videowidth', 640),
    '#size' => 4,
  );
  $form['teacherstv_settings']['teacherstv_videoheight'] = array(
    '#type' => 'textfield',
    '#title' => t('Default height (in pixels)'),
    '#default_value' => variable_get('teacherstv_videoheight', 480),
    '#size' => 4,
  );

  $form['backup_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Backup settings (CURRENTLY DISABLED)'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#weight' => 20,
  );

    if (variable_get('teacherstv_backup', '')) {
        $default = 'enable';
    }
    else{
        $default = 0;
    }

    $form['backup_settings']['teacherstv_backup'] = array(
        '#type' => 'checkbox',
        '#title' => t('Enable video backup (Currently disabled)'),
        '#default_value' => variable_get('teacherstv_backup'),
    );

    $form['backup_settings']['teacherstv_backup_location'] = array(
        '#type' => 'textfield',
        '#title' => t('Backup Location'),
        '#size' => 35,
        '#default_value' => variable_get('teacherstv_backup_location', ''),
    );


  $form['#validate'][] = 'teacherstv_settings_form_validate';
  $form['#submit'][] = 'teacherstv_settings_form_submit';

  $form = system_settings_form($form);

  return $form;

}
/**
  * Validate settings form values
  *
  * TODO
  *   * Test connection to TeachersTV API.
  *   * Test that backup location is writable.
  *
  */
function teacherstv_settings_form_validate($form, &$form_state) {
  // The salt should be 16 characters or more.
  if (strlen($form_state['values']['teacherstv_cryptkey']) <> 16) {
    form_set_error('teacherstv_cryptkey',t('The API salt must be 16 characters.'));
  }

  $apiurl = $form_state['values']['teacherstv_apiurl'];
  $apiuser = $form_state['values']['teacherstv_apiusername'];
  $apipass = $form_state['values']['teacherstv_apipassword'];

  if (teacherstv_test_connection($apiurl, $apiuser, $apipass) === 0) {
    form_set_error('teacherstv_apiurl', t('Could not connect to TeachersTV API.  Please check the URL.'));
  }
  if (teacherstv_test_connection($apiurl, $apiuser, $apipass) === 401) {
    form_set_error('teacherstv_apiusername', t('Could not connect to TeachersTV API.  Please check the username and password'));
    form_set_error('teacherstv_apipassword', '');
  }
  if (teacherstv_test_connection($apiurl, $apiuser, $apipass) === 404) {
    form_set_error('teacherstv_apiurl', t('Could not connect to TeachersTV API.  Please check the URL.'));
  }


}

function teacherstv_settings_form_submit($form, &$form_state) {
  // encrypt and save the password as a variable
  $password = teacherstv_store_password($form_state['values']['teacherstv_apipassword']);
  // Must unset the $form_state value, as it will overwrite the password otherwise.
  unset($form_state['values']['teacherstv_apipassword']);



}





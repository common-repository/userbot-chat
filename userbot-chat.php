<?php

/*
Plugin Name: Userbot Chat
Description: This is the official Userbot plugin for Wordpress CMS.
Version: 1.0.4
Author: Userbot Srl
Author URI: https://userbot.ai
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

/**
 * Plugin activation hook
 * @since 1.0.0
 */
function userbot_onPluginActivation() {}

/**
 * Plugin deactivation hook
 * @since 1.0.0
 */
function userbot_onPluginDeactivation() {}

/**
 * Plugin uninstall hook
 * @since 1.0.0
 */
function userbot_onPluginUninstall() {}

/**
 * Create option page
 * @since 1.0.0
 */
function userbot_plugin_settings() {
  $pages = array(
    array(
      'page_title'  => 'Userbot Chat',
      'menu_title'  => 'Userbot',
      'capability'  => 'administrator',
      'menu_slug'   => 'userbot_settings',
      'function'    => 'userbot_plugin_settings_display',
      'icon_url'    => plugins_url('/src/assets/favicon-32.png', __FILE__),
      'position'    => 99
    )
  );
  foreach ($pages as $page) {
    add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['function'], $page['icon_url'], $page['position']);
  }
}

/**
 * Generate option page
 * @since 1.0.0
 */
function userbot_plugin_settings_display() {
  $api_key = (get_option('userbot_api_key') !== '') ? get_option('userbot_api_key') : '';
  $api_customer = (get_option('userbot_api_customer') !== '') ? get_option('userbot_api_customer') : '';
  $markup = '
    <div class="userbot-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-sm-12">
            <div class="userbot-logo-container mb-4">
              <img src="' . plugins_url('/src/assets/userbot-horizontal-color@4x.png', __FILE__) . '" alt="" class="img-fluid">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <h1 class="userbot-primary-color">Account settings</h1>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-sm-12 col-md-8">
            <form action="options.php" method="post" name="options" class="form-table">
              ' . wp_nonce_field('update-options') . '
              <div class="form-group">
                <label for="userbot_api_key">API Key</label>
                <input 
                  type="text"
                  class="form-control" 
                  placeholder="Insert your API Key" 
                  name="userbot_api_key" 
                  id="userbot_api_key" 
                  value="' . esc_attr($api_key) . '">
              </div>
              <div class="form-group">
                <label for="userbot_api_customer">API Customer</label>
                <input 
                  type="text"
                  class="form-control" 
                  placeholder="Insert your API Customer" 
                  name="userbot_api_customer" 
                  id="userbot_api_customer" 
                  value="' . esc_attr($api_customer) . '">
              </div>
              <div class="form-group d-flex justify-content-end align-items-center">
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="userbot_api_key,userbot_api_customer" />';
  if (isset($_GET['settings-updated'])) {
    $markup .= '
      <p class="userbot-alert" role="alert">
        Your settings has been saved!
      </p>
    ';
  }
  $markup .= '
                <input type="submit" name="Submit" id="Submit" class="btn userbot-button" value="Save settings">
              </div>
            </form>
            <small class="form-text text-muted">
                Not sure where to retrieve your API Key and your Customer API? <br />
                Log in with your account on <a href="' . esc_url('https://my.userbot.ai') . '" target="_blank">Userbot</a> and go to the <strong>Integrations</strong> page to retrieve the necessary credentials.
            </small>
          </div>
        </div>
      </div>
    </div>
  ';
  echo $markup;
}

/**
 * Enqueue public scripts and pass data through wp_localize_script
 * @since 1.0.0
 */
function userbot_addScripts() {
  $external_scripts = array(
//    array(
//      'handle'    =>  'userbot',
//      'src'       =>  'https://cdn.userbot.ai/widget-chat/dist/userbot.js',
//      'deps'      =>  array(),
//      'version'   =>  false,
//      'in_footer' =>  true
//    )
  );
  $local_scripts = array(
    array(
      'handle'    =>  'userbot',
      'src'       =>  '/src/js/chat.min.js',
      'deps'      =>  array(),
      'version'   =>  false,
      'in_footer' =>  true
    )
  );
  if (get_option('userbot_api_key') !== '' && get_option('userbot_api_customer') !== '') {
    $script = array(
      'handle'    =>  'userbot_initialize_chat',
      'src'       =>  '/src/js/userbot.initialize.js',
      'deps'      =>  array('userbot'),
      'version'   =>  false,
      'in_footer' =>  true
    );
    array_push($local_scripts, $script);
  }
  foreach ($external_scripts as $script) {
    wp_register_script($script['handle'], $script['src'], $script['deps'], $script['version'], $script['in_footer']);
  }
  foreach ($local_scripts as $script) {
    wp_register_script($script['handle'], plugins_url($script['src'], __FILE__), $script['deps'], $script['version'], $script['in_footer']);
    wp_enqueue_script($script['handle']);
  }
  if (get_option('userbot_api_key') !== '' && get_option('userbot_api_customer') !== '') {
    $dataToBePassed = array(
      'key'       => get_option('userbot_api_key'),
      'customer'  => get_option('userbot_api_customer')
    );
    wp_localize_script('userbot_initialize_chat', 'customer_credentials', $dataToBePassed);
  }
}

/**
 * Enqueue administration styles and scripts
 * @since 1.0.0
 */
function userbot_addAdminScripts() {
  $admin_scripts = array();
  $admin_styles = array(
    array(
      'handle'    => 'bootstrap',
      'src'       => plugins_url('/src/libs/bootstrap/bootstrap.min.css', __FILE__)
    ),
    array(
      'handle'    => 'userbot_admin_style',
      'src'       => plugins_url('/src/css/style.admin.css', __FILE__)
    )
  );
  foreach ($admin_scripts as $script) {
    wp_register_script($script['handle'], plugins_url($script['src'], __FILE__), $script['deps'], $script['version'], $script['in_footer']);
  }
  foreach ($admin_scripts as $script) {
    wp_enqueue_script($script['handle']);
  }
  foreach ($admin_styles as $style) {
    wp_enqueue_style($style['handle'], $style['src']);
  }
}

/**
 * Register actions
 * @since 1.0.0
 */
add_action('admin_menu', 'userbot_plugin_settings');
add_action('wp_enqueue_scripts', 'userbot_addScripts');
add_action('admin_enqueue_scripts', 'userbot_addAdminScripts');

/**
 * Register hooks
 * @since 1.0.0
 */
register_activation_hook(__FILE__, 'userbot_onPluginActivation');
register_deactivation_hook(__FILE__, 'userbot_onPluginDeactivation');
register_uninstall_hook( __FILE__, 'userbot_onPluginUninstall');

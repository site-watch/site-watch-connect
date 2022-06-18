<?php

namespace Sw_Admin_Form\Inc\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://www.sitewatch.app
 * @since      1.0.0
 *
 * @author     Site Watch
 */
class Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The text domain of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_text_domain    The text domain of this plugin.
     */
    private $plugin_text_domain;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string $plugin_name	The name of this plugin.
     * @param    string $version	The version of this plugin.
     * @param	 string $plugin_text_domain	The text domain of this plugin
     */
    public function __construct($plugin_name, $version, $plugin_text_domain)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_text_domain = $plugin_text_domain;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        // wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/file.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        // $params = array( 'ajaxurl' => admin_url('admin-ajax.php') );
        // wp_enqueue_script('site_watch_ajax_handle', plugin_dir_url(__FILE__) . 'js/file.js', array( 'jquery' ), $this->version, false);
        // wp_localize_script('site_watch_ajax_handle', 'params', $params);
    }

    /**
     * Callback for the admin menu
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu()
    {
        add_options_page('Site Watch', 'Site Watch', 'manage_options', 'site-watch-connect', [$this,'html_form_page_content']);
    }

    /*
     * Callback for the add_submenu_page action hook
     *
     * The plugin's HTML form is loaded from here
     *
     * @since	1.0.0
     */
    public function html_form_page_content()
    {
        //show the form
        include_once('views/partials-html-form-view.php');
    }

    /**
     *
     * @since    1.0.0
     */
    public function the_form_response()
    {
        if (isset($_POST['sw_add_user_meta_nonce']) && wp_verify_nonce($_POST['sw_add_user_meta_nonce'], 'sw_add_user_meta_form_nonce')) {
            $originalKey = $this->randomCharacter(30);
            $_POST['key'] = $originalKey;

            update_option('site_watch_key', wp_hash_password($originalKey), 'no');

            $admin_notice = "success";

            $this->custom_redirect($admin_notice, $_POST);
            exit;
        } else {
            wp_die(__('Invalid nonce specified', $this->plugin_name), __('Error', $this->plugin_name), array(
                'response' 	=> 403,
                'back_link' => 'admin.php?page=' . $this->plugin_name,
            ));
        }
    }

    /**
     * Redirect
     *
     * @since    1.0.0
     */
    public function custom_redirect($admin_notice, $response)
    {
        wp_redirect(esc_url_raw(add_query_arg(
            array(
                'site_watch_admin_add_notice' => $admin_notice,
                'site_watch_response' => $response,
            ),
            admin_url('options-general.php?page='. $this->plugin_name)
        )));
    }


    /**
     * Print Admin Notices
     *
     * @since    1.0.0
     */
    public function print_plugin_admin_notices()
    {
        if (isset($_REQUEST['site_watch_admin_add_notice'])) {
            if ($_REQUEST['site_watch_admin_add_notice'] === "success") {
                // notice notice-success is-dismissible
                $html =	'<div class="card">
				<h2>Your new key has been generated:</h2>';
                $html .= "<h3 style='color:#737373;'>".htmlspecialchars($_REQUEST['site_watch_response']['key'])."</h3>";
                $html .= "<p>This key is only displayed <strong>once</strong>, so please record it securely.</p><p>If you misplace this key, please regenerate.</p>";
                $html .= "</div>";
                echo $html;
            }

            // handle other types of form notices
        } else {
            return;
        }
    }

    private function randomCharacter($length)
    {
        $chars = implode(range('a', 'z'));
        $chars = implode(range('a', 'z'));
        $chars .= implode(range('0', '9'));

        $shuffled = str_shuffle($chars);
        return substr($shuffled, 0, $length);
    }
}

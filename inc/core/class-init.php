<?php

namespace Sw_Admin_Form\Inc\Core;

use Sw_Admin_Form as NS;
use Sw_Admin_Form\Inc\Admin as Admin;
use Sw_Admin_Form\Inc\Core as Core;

/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @link       https://www.sitewatch.app
 * @since      1.0.0
 *
 * @author     Site Watch
 */
class Init
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    0.0.1
     * @access   protected
     * @var      string    $plugin_base_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_basename;

    /**
     * The current version of the plugin.
     *
     * @since    0.0.1
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The text domain of the plugin.
     *
     * @since    0.0.1
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $plugin_text_domain;


    // define the core functionality of the plugin.
    public function __construct()
    {
        $this->plugin_name = NS\PLUGIN_NAME;
        $this->version = NS\PLUGIN_VERSION;
        $this->plugin_basename = NS\PLUGIN_BASENAME;
        $this->plugin_text_domain = NS\PLUGIN_TEXT_DOMAIN;

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        // $this->define_public_hooks();
    }

    /**
     * Loads the following required dependencies for this plugin.
     *
     * - Loader - Orchestrates the hooks of the plugin.
     * - Internationalization_i18n - Defines internationalization functionality.
     * - Admin - Defines all hooks for the admin area.
     *
     * @access    private
     */
    private function load_dependencies()
    {
        $this->loader = new Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Internationalization_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @access    private
     */
    private function set_locale()
    {
        $plugin_i18n = new Internationalization_i18n($this->plugin_text_domain);

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * Callbacks are documented in inc/admin/class-admin.php
     *
     * @access    private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Admin\Admin(
            $this->get_plugin_name(),
            $this->get_version(),
            $this->get_plugin_text_domain()
        );

        $plugin_api = new Core\Api(
            $this->get_plugin_name(),
            $this->get_version(),
            $this->get_plugin_text_domain()
        );

        // $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        // $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        //Add a top-level admin menu for our plugin
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');

        //when a form is submitted to admin-post.php
        $this->loader->add_action('admin_post_sw_form_response', $plugin_admin, 'the_form_response');

        // Register admin notices
        $this->loader->add_action('admin_notices', $plugin_admin, 'print_plugin_admin_notices');

        // Register new endpoint
        $this->loader->add_action('rest_api_init', $plugin_api, 'register_routes');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Retrieve the text domain of the plugin.
     *
     * @since     1.0.0
     * @return    string    The text domain of the plugin.
     */
    public function get_plugin_text_domain()
    {
        return $this->plugin_text_domain;
    }
}

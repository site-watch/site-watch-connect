<?php

namespace Sw_Admin_Form\Inc\Core;

use Sw_Admin_Form as NS;
use Sw_Admin_Form\Inc\Admin as Admin;

/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @link       https://www.sitewatch.app
 * @since      1.0.0
 *
 * @author     Site Watch
 */
class Api
{
    /**
     * Initialize the collections used to maintain the actions and filters.
     *
     * @since    0.0.1
     */
    public function __construct($plugin_name, $version, $plugin_text_domain)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_text_domain = $plugin_text_domain;
    }

    /**
     * Register API endpoints
     *
     * @since    0.0.1
     */
    public function run()
    {
        $this->register_routes();
    }

    public function register_routes()
    {
        register_rest_route('sitewatch/v1', '/features', array(
            'methods' => 'GET',
            'callback' => [$this, 'get_details'],
            'permission_callback' => [$this, 'permission_check']
        ));
    }

    /**
     * permission_callback for access validation
     *
     * @param object $request
     * @return void
     * @since    1.1.0
     */
    public function permission_check($request)
    {
        $authorizationHeader = $request->get_header('authorization');

        if (!$authorizationHeader || $authorizationHeader == "Bearer") {
            return new \WP_Error('rest_forbidden', esc_html__('Please provide a key', 'site-watch-plugin'), array('status' => 401));
        }

        return $this->validate_key($authorizationHeader);
    }

    /**
     * Check the authentication header sent to the endpoint against the hashed key
     *
     * @param string $authorizationHeader
     * @return void
     * @since    1.1.0
     */
    public function validate_key($authorizationHeader)
    {
        $key = get_option('site_watch_key');

        if (!$key) {
            return new \WP_Error('rest_forbidden', esc_html__('Site Watch key has not yet been generated', 'site-watch-plugin'), array('status' => 403));
        }

        // substr to remove "Bearer"
        if (wp_check_password(substr($authorizationHeader, 7, 30), $key)) {
            return true;
        } else {
            return new \WP_Error('rest_unauthorized', esc_html__('Access denied', 'site-watch-plugin'), array('status' => 401));
        }
    }

    public function get_details($request)
    {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        $response['urls'] = $this->wordpress_urls();
        $response['site-watch-connect-version'] = $this->site_watch_connect_version();
        $response['core'] = $this->wordpress_core();
        $response['plugins'] = $this->wordpress_plugins();
        $response['health'] = $this->wordpress_health();
        $response['php'] = phpversion();
        $response['jetpack-protect'] = $this->jetpack_protect_metrics();

        return $response;
    }

    public function site_watch_connect_version()
    {
        return NS\PLUGIN_VERSION;
    }

    public function wordpress_urls()
    {
        $urls['homepage'] = get_home_url(null);
        $urls['admin'] = get_admin_url(null);
        $urls['site-health'] = get_admin_url(null, 'site-health.php');
        $urls['plugins'] = get_admin_url(null, 'plugins.php');
        $urls['updates'] = get_admin_url(null, 'update-core.php');
        return $urls;
    }

    public function wordpress_core()
    {
        global $wp_version;
        return $wp_version;
    }

    public function wordpress_plugins()
    {
        $plugin_response = get_plugins();

        $active_plugins = get_option('active_plugins');

        $update_status = get_site_transient('update_plugins');

        if (count($plugin_response) > 0) {
            // Add details to see which plugins are active
            foreach ($plugin_response as $key => $plugin) {
                if (in_array($key, $active_plugins)) {
                    $plugin_response[$key]['Active'] = true;
                } else {
                    $plugin_response[$key]['Active'] = false;
                }
            }

            // Add details to see if this WP site thinks there is an update available
            if ($update_status) {
                if (count($update_status->response) > 0) {
                    foreach ($plugin_response as $key => $plugin) {
                        if (isset($update_status->response[$key])) {
                            $plugin_response[$key]['UpdateAvailable'] = $update_status->response[$key]->new_version;
                        } else {
                            $plugin_response[$key]['UpdateAvailable'] = false;
                        }
                    }
                }
            }
        }

        return $plugin_response;
    }

    public function wordpress_health()
    {
        $health_data = get_transient('health-check-site-status-result');
        return json_decode($health_data, true);
    }

    public function jetpack_protect_metrics()
    {
        if (is_plugin_active('jetpack-protect/jetpack-protect.php')) {
            if (\get_option('jetpack_protect_status') != false) {
                return unserialize(\get_option('jetpack_protect_status'));
            } else {
                return [];
            }
        } else {
            return null;
        }
    }
}

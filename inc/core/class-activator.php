<?php

namespace Sw_Admin_Form\Inc\Core;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.

 * @link       https://www.sitewatch.app
 * @since      1.0.0
 *
 * @author     Site Watch
 */

class Activator
{
    /**
     * Short Description.
     *
     * Long Description.
     *
     * @since    0.0.1
     */
    public static function activate()
    {

        $min_php = '5.6.0';

        // Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
        if (version_compare(PHP_VERSION, $min_php, '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die('This plugin requires a minmum PHP Version of ' . $min_php);
        }

    }

}

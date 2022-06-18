<?php
/**
 *
 * @link              https://www.sitewatch.app
 * @since             1.0.0
 * @package           Site_Watch_Connect
 *
 * @wordpress-plugin
 * Plugin Name:       Site Watch Connect
 * Plugin URI:        https://www.sitewatch.app/
 * Description:       A simple plugin to demo the use of forms in the Admin area of WordPress
 * Version:           1.0.0
 * Author:            Site Watch Team
 * Author URI:        https://www.sitewatch.app/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       site-watch-connect
 * Domain Path:       /languages
 */

namespace Sw_Admin_Form;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Define Constants
 */

define(__NAMESPACE__ . '\NS', __NAMESPACE__ . '\\');

define(NS . 'PLUGIN_NAME', 'site-watch-connect');

define(NS . 'PLUGIN_VERSION', '1.0.0');

define(NS . 'PLUGIN_NAME_DIR', plugin_dir_path(__FILE__));

define(NS . 'PLUGIN_NAME_URL', plugin_dir_url(__FILE__));

define(NS . 'PLUGIN_BASENAME', plugin_basename(__FILE__));

define(NS . 'PLUGIN_TEXT_DOMAIN', 'site-watch-connect');

require_once(PLUGIN_NAME_DIR . 'inc/core/class-init.php');
require_once(PLUGIN_NAME_DIR . 'inc/core/class-loader.php');
require_once(PLUGIN_NAME_DIR . 'inc/core/class-internationalization-i18n.php');
require_once(PLUGIN_NAME_DIR . 'inc/core/class-api.php');
require_once(PLUGIN_NAME_DIR . 'inc/admin/class-admin.php');


/**
 * Register Activation and Deactivation Hooks
 * This action is documented in inc/core/class-activator.php
 */

register_activation_hook(__FILE__, array( NS . 'Inc\Core\Activator', 'activate' ));

/**
 * The code that runs during plugin deactivation.
 * This action is documented inc/core/class-deactivator.php
 */

register_deactivation_hook(__FILE__, array( NS . 'Inc\Core\Deactivator', 'deactivate' ));


/**
 * Plugin Singleton Container
 *
 * Maintains a single copy of the plugin app object
 *
 * @since    1.0.0
 */
class Sw_Admin_Form
{
    public static $init;
    /**
     * Loads the plugin
     *
     * @access    public
     */
    public static function init()
    {
        if (null == self::$init) {
            self::$init = new Inc\Core\Init();
            self::$init->run();
        }

        return self::$init;
    }
}

/*
 * Begins execution of the plugin
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the plugin's hooks contained within.
 *
 */
function Sw_Admin_Form_init()
{
    return Sw_Admin_Form::init();
}

$min_php = '5.8.0';

// Check the minimum required PHP version and run the plugin.
if (version_compare(PHP_VERSION, $min_php, '>=')) {
    Sw_Admin_Form_init();
}

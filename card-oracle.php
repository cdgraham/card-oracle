<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://cdgraham.com
 * @since             0.11.0
 * @package           Card_Oracle
 *
 * @wordpress-plugin
 * Plugin Name:       Card Oracle
 * Plugin URI:        https://chillichalli.com/card-oracle
 * Description:       This plugin lets you create tarot and oracle readings using your own cards, spreads and interpretations.
 * Version:           0.11.0
 * Author:            Christopher Graham
 * Author URI:        https://cdgraham.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       card-oracle
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * Currently plugin version.
 * Start at version 0.5.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if ( !defined( 'CARD_ORACLE_VERSION' ) ) {
    define( 'CARD_ORACLE_VERSION', '0.11.0' );
}
/**
 * Setup the Freemius SDK integration
 * 
 * @since    0.7.2
 */

if ( !function_exists( 'card_oracle_fs' ) ) {
    // Create a helper function for easy SDK access.
    function card_oracle_fs()
    {
        global  $card_oracle_fs ;
        
        if ( !isset( $card_oracle_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $card_oracle_fs = fs_dynamic_init( array(
                'id'              => '6063',
                'slug'            => 'card-oracle',
                'type'            => 'plugin',
                'public_key'      => 'pk_f85b93aca5d64925a5271e7ca7e01',
                'is_premium'      => false,
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'has_affiliation' => 'selected',
                'menu'            => array(
                'slug' => 'card-oracle-admin-menu',
            ),
                'is_live'         => true,
            ) );
        }
        
        return $card_oracle_fs;
    }
    
    // Init Freemius.
    card_oracle_fs();
    // Signal that SDK was initiated.
    do_action( 'card_oracle_fs_loaded' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-card-oracle-activator.php
 */
function activate_card_oracle()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-card-oracle-activator.php';
    Card_Oracle_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-card-oracle-deactivator.php
 */
function deactivate_card_oracle()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-card-oracle-deactivator.php';
    Card_Oracle_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_card_oracle' );
register_deactivation_hook( __FILE__, 'deactivate_card_oracle' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-card-oracle.php';
/**
 * Include the external APIs like mailchimp
 */
//require plugin_dir_path( __FILE__ ) . 'includes/class-mailchimp__premium_only.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.5.0
 */
function run_card_oracle()
{
    $plugin = new Card_Oracle();
    $plugin->run();
}

run_card_oracle();
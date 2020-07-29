<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://cdgraham.com
 * @since      0.7.0
 *
 * @package    Card_Oracle
 * @subpackage Card_Oracle/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.5.0
 * @package    Card_Oracle
 * @subpackage Card_Oracle/includes
 * @author     Christopher Graham <chris@chillichalli.com>
 */
class Card_Oracle
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    0.5.0
     * @access   protected
     * @var      Card_Oracle_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected  $loader ;
    /**
     * The unique identifier of this plugin.
     *
     * @since    0.5.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected  $plugin_name ;
    /**
     * The current version of the plugin.
     *
     * @since    0.5.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected  $version ;
    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    0.5.1
     */
    public function __construct()
    {
        
        if ( defined( 'CARD_ORACLE_VERSION' ) ) {
            $this->version = CARD_ORACLE_VERSION;
        } else {
            $this->version = '0.11.0';
        }
        
        $this->plugin_name = 'card-oracle';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        // Check update when plugin loaded
        $this->card_oracle_check_version();
    }
    
    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Card_Oracle_Loader. Orchestrates the hooks of the plugin.
     * - Card_Oracle_i18n. Defines internationalization functionality.
     * - Card_Oracle_Admin. Defines all hooks for the admin area.
     * - Card_Oracle_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    0.5.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-card-oracle-loader.php';
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-card-oracle-i18n.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-card-oracle-admin.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-card-oracle-public.php';
        /**
         * The class responsible for defining all actions that occur in the meta boxes
         * side of the site.
         */
        $this->loader = new Card_Oracle_Loader();
    }
    
    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Card_Oracle_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    0.5.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Card_Oracle_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }
    
    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    0.7.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Card_Oracle_Admin( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        // Call Freemius uninstall
        card_oracle_fs()->add_action( 'after_uninstall', 'card_oracle_fs_uninstall_cleanup' );
        // call custom post types
        $this->loader->add_action( 'init', $plugin_admin, 'register_card_oracle_cpt' );
        // Custom columns for admin screens
        $this->loader->add_filter( 'manage_edit-co_cards_columns', $plugin_admin, 'set_custom_cards_columns' );
        $this->loader->add_filter( 'manage_edit-co_cards_sortable_columns', $plugin_admin, 'set_custom_sortable_card_columns' );
        $this->loader->add_filter( 'manage_edit-co_descriptions_columns', $plugin_admin, 'set_custom_descriptions_columns' );
        $this->loader->add_filter( 'manage_edit-co_descriptions_sortable_columns', $plugin_admin, 'set_custom_sortable_description_columns' );
        $this->loader->add_filter( 'manage_edit-co_readings_columns', $plugin_admin, 'set_custom_readings_columns' );
        $this->loader->add_filter( 'manage_edit-co_positions_columns', $plugin_admin, 'set_custom_positions_columns' );
        $this->loader->add_filter( 'manage_edit-co_positions_sortable_columns', $plugin_admin, 'set_custom_sortable_position_columns' );
        $this->loader->add_action( 'manage_co_cards_posts_custom_column', $plugin_admin, 'custom_card_column' );
        $this->loader->add_action( 'manage_co_descriptions_posts_custom_column', $plugin_admin, 'custom_card_column' );
        $this->loader->add_action( 'manage_co_readings_posts_custom_column', $plugin_admin, 'custom_card_column' );
        $this->loader->add_action( 'manage_co_positions_posts_custom_column', $plugin_admin, 'custom_card_column' );
        // Add Menu items
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'card_oracle_menu_items' );
        // Add Options items
        //$this->loader->add_action( 'admin_init', $plugin_admin, 'card_oracle_setup_sections' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'card_oracle_setup_general_options' );
        // Add metaboxes
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes_for_readings_cpt' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes_for_positions_cpt' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes_for_cards_cpt' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes_for_descriptions_cpt' );
        $this->loader->add_action( 'do_meta_boxes', $plugin_admin, 'cpt_image_box' );
        $this->loader->add_action( 'save_post', $plugin_admin, 'save_card_oracle_meta_data' );
        // Limit number of Readings and Positions
        $this->loader->add_action( 'wp_insert_post', $plugin_admin, 'limit_positions_cpt_count' );
        // Add our card_oracle_display_admin_notices function to the admin_notices
        //$this->loader->add_action( 'admin_notices', $plugin_admin, 'card_oracle_display_admin_notices', 12 );
        // Demo Data
        $this->loader->add_action( 'admin_action_demo_data', $plugin_admin, 'card_oracle_demo_data' );
    }
    
    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    0.5.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Card_Oracle_Public( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        // Add the shortchode
        $this->loader->add_shortcode( 'card-oracle', $plugin_public, 'display_card_oracle_set' );
        $this->loader->add_shortcode( 'card-oracle-daily', $plugin_public, 'display_card_oracle_card_of_day' );
        $this->loader->add_shortcode( 'card-oracle-random', $plugin_public, 'display_card_oracle_random_card' );
        // Add Ajax for sending emails
        $this->loader->add_action( 'wp_ajax_send_reading_email', $plugin_public, 'card_oracle_send_reading_email' );
        $this->loader->add_action( 'wp_ajax_nopriv_send_reading_email', $plugin_public, 'card_oracle_send_reading_email' );
    }
    
    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    0.5.0
     * @return	 void
     */
    public function run()
    {
        $this->loader->run();
    }
    
    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.5.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }
    
    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     0.5.0
     * @return    Card_Oracle_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }
    
    /**
     * Retrieve the version number of the plugin.
     *
     * @since     0.5.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
    
    /**
     * Update anything after the version number of the plugin changes.
     *
     * @since     0.5.0
     * @return    void
     */
    public function card_oracle_check_version()
    {
        // if the version in the db and the plugin version are different do updates
        $current_version = get_option( 'card_oracle_version' );
        
        if ( $this->get_version() !== $current_version ) {
            /**  
             * Add any updates required to the DB or options here when 
             * updating from one version to another
             */
            // Updated wordpress options, add card_oracle_ prefix to avoid clashes with other plugins. Version 0.7.2
            
            if ( version_compare( $current_version, '0.7.2', '<' ) ) {
                $old_options = array(
                    'multiple_positions',
                    'allow_email',
                    'from_email',
                    'from_email_name',
                    'email_subject',
                    'email_text',
                    'email_success'
                );
                foreach ( $old_options as $old_option ) {
                    
                    if ( get_option( $old_option ) ) {
                        // Update the option name by adding the new name and deleting the old option name
                        update_option( 'card_oracle_' . $old_option, get_option( $old_option ) );
                        delete_option( $old_option );
                    }
                
                }
            }
            
            update_option( 'card_oracle_version', CARD_ORACLE_VERSION );
        }
    
    }
    
    public function card_oracle_fs_uninstall_cleanup()
    {
        // Delete the Card Oracle Version from the options
        delete_option( 'card_oracle_version' );
        // Delete and CPT data
        $sql = "DELETE posts, terms, meta\r\n\t\tFROM " . $wpdb->posts . " posts\r\n\t\tLEFT JOIN " . $wpdb->term_relationships . " terms\r\n\t\t\tON ( posts.ID = terms.object_id )\r\n\t\tLEFT JOIN " . $wpdb->postmeta . " meta\r\n\t\t\tON ( posts.ID = meta.post_id )\r\n\t\tWHERE posts.post_type in ( 'co_cards', 'co_descriptions', 'co_positions', 'co_readings' );";
        $wpdb->get_results( $sql, OBJECT );
    }

}
<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cdgraham.com
 * @since      0.7.0
 *
 * @package    Card_Oracle
 * @subpackage Card_Oracle/admin
 */
use  mailchimp\mailchimp ;
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Card_Oracle
 * @subpackage Card_Oracle/admin
 * @author     Christopher Graham <chris@chillichalli.com>
 */
class Card_Oracle_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    0.5.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    0.5.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @since	0.5.0
     * @param	string	$plugin_name	The name of this plugin.
     * @param	string	$version		The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-persistent-notices.php';
    }
    
    /**
     * Writes to WP error_log
     *
     * @since	0.9.0
     * @return	void
     */
    public function card_oracle_write_log( $log )
    {
        
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    
    }
    
    /**
     * Add a flash notice to {prefix}options table until a full page refresh is done
     *
     * @param string $notice our notice message
     * @param string $type This can be "info", "warning", "error" or "success", "warning" as default
     * @param boolean $dismissible set this to TRUE to add is-dismissible functionality to your notice
     * @return void
     */
    function card_oracle_add_admin_notice( $message = "", $type = "warning", $dismissible = true )
    {
        // Here we return the notices saved on our option, if there are not notices, then an empty array is returned
        $dismissible_text = ( $dismissible ? "is-dismissible" : "" );
        $notice = array(
            "notice"      => $message,
            "type"        => $type,
            "dismissible" => $dismissible_text,
        );
        $notices = get_option( 'card_oracle_admin_notice' );
        if ( empty($notices) ) {
            $notices = array();
        }
        // We add our new notice.
        array_push( $notices, $notice );
        // Then we update the option with our notices array
        update_option( 'card_oracle_admin_notice', $notices );
    }
    
    /**
     * Function executed when the 'admin_notices' action is called, here we check if there are notices on
     * our database and display them, after that, we remove the option to prevent notices being displayed forever.
     * @return void
     */
    function card_oracle_display_admin_notices_two()
    {
        // get the current screen we are on
        $screen = get_current_screen();
        $this->card_oracle_write_log( 'Base ' . $screen->base );
        $this->card_oracle_write_log( 'Parent Base ' . $screen->parent_base );
        // Get the current notices
        $notices = get_option( 'card_oracle_admin_notice' );
        // If there are notices and we are on the Card Oracle Admin Menu display them
        
        if ( !empty($notices) && $screen->base === 'toplevel_page_card-oracle-admin-menu' ) {
            // Iterate through our notices to be displayed and print them.
            foreach ( $notices as $notice ) {
                printf(
                    '<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
                    $notice['type'],
                    $notice['dismissible'],
                    $notice['notice']
                );
            }
            // Now we reset our options to prevent notices being displayed forever.
            delete_option( 'card_oracle_admin_notice' );
        }
    
    }
    
    function card_oracle_display_admin_notices()
    {
        return '<div class="notice notice-success is-dismissible">
			<p>The secret to success is to know something nobody else knows ~ Aristotle Onassis</p>
		</div>';
    }
    
    /**
     * Add an options page under the Card Oracle menu
     *
     * @since	0.5.0
     * @return	void
     */
    public function add_card_oracle_options_page()
    {
        $this->plugin_screen_hook_suffix = add_options_page(
            __( 'Card Oracle Settings', 'card-oracle' ),
            __( 'Card Oracle', 'card-oracle' ),
            'manage_options',
            $this->plugin_name,
            array( $this, 'display_card_oracle_options_page' )
        );
    }
    
    /**
     * Get the total counts of a cpt
     * 
     * @since	0.5.0
     * @param	string	$card_oracle_cpt	The name of the custom post type.
     * @return	int							The count of custom post types.
     */
    public function get_card_oracle_cpt_count( $card_oracle_cpt )
    {
        return wp_count_posts( $card_oracle_cpt )->publish;
    }
    
    /**
     * Render the options page for plugin
     *
     * @since	0.7.0
     * @return	void
     */
    public function display_card_oracle_options_page()
    {
        global  $wpdb ;
        $reading_array = array();
        $screen = get_current_screen();
        $active_tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'dashboard' );
        $tabs = array( array(
            'uid'      => 'dashboard',
            'name'     => __( 'Dashboard', 'card-oracle' ),
            'htmlfile' => 'partials/card-oracle-tab-dashboard.php',
        ), array(
            'uid'      => 'general',
            'name'     => __( 'General', 'card-oracle' ),
            'htmlfile' => 'partials/card-oracle-tab-general.php',
        ) );
        // Add WP thickbox control
        add_thickbox();
        $readings_count = number_format_i18n( $this->get_card_oracle_cpt_count( 'co_readings' ) );
        /* translators: %d is a number */
        $readings_text = esc_html( sprintf( _n(
            '%d Total',
            '%d Total',
            $readings_count,
            'card-oracle'
        ), $readings_count ) );
        $cards_count = number_format_i18n( $this->get_card_oracle_cpt_count( 'co_cards' ) );
        /* translators: %d is a number */
        $cards_text = esc_html( sprintf( _n(
            '%d Total',
            '%d Total',
            $cards_count,
            'card-oracle'
        ), $cards_count ) );
        $positions_count = number_format_i18n( $this->get_card_oracle_cpt_count( 'co_positions' ) );
        /* translators: %d is a number */
        $positions_text = esc_html( sprintf( _n(
            '%d Total',
            '%d Total',
            $positions_count,
            'card-oracle'
        ), $positions_count ) );
        $descriptions_count = number_format_i18n( $this->get_card_oracle_cpt_count( 'co_descriptions' ) );
        /* translators: %d is a number */
        $descriptions_text = esc_html( sprintf( _n(
            '%d Total',
            '%d Total',
            $descriptions_count,
            'card-oracle'
        ), $descriptions_count ) );
        $readings = $this->get_card_oracle_posts_by_cpt( 'co_readings', 'post_title' );
        for ( $i = 0 ;  $i < count( $readings ) ;  $i++ ) {
            $reading_array[$i] = new stdClass();
            $reading_array[$i]->positions = count( $this->get_card_oracle_posts_by_cpt(
                'co_positions',
                NULL,
                NULL,
                '_co_reading_id',
                $readings[$i]->ID,
                'LIKE'
            ) );
            $reading_array[$i]->cards = count( $this->get_card_oracle_posts_by_cpt(
                'co_cards',
                'title',
                'ASC',
                '_co_reading_id',
                $readings[$i]->ID,
                'LIKE'
            ) );
            $reading_array[$i]->descriptions = count( $this->get_co_description_ids( $readings[$i]->ID ) );
        }
        // Include the Header file
        include_once 'partials/card-oracle-admin-header.php';
        // Display the tabs
        echo  '<h2 class="nav-tab-wrapper">' ;
        foreach ( $tabs as $tab ) {
            $class = ( $tab['uid'] == $active_tab ? ' nav-tab-active' : '' );
            echo  '<a class="nav-tab' . $class . '" href="?page=card-oracle-admin-menu&tab=' . $tab['uid'] . '">' . $tab['name'] . '</a>' ;
        }
        echo  '</h2>' ;
        // End tab display
        // include the html files for each of the tabs
        foreach ( $tabs as $tab ) {
            include_once plugin_dir_path( __FILE__ ) . $tab['htmlfile'];
        }
        include_once 'partials/card-oracle-admin-footer.php';
    }
    
    /**
     * Callbacks for the admin display sections
     *
     * @since	0.7.0
     * @return	void
     */
    public function card_oracle_section_callback( $arguments )
    {
        switch ( $arguments['id'] ) {
            case 'general_section':
                break;
            case 'email_section':
                break;
        }
    }
    
    /**
     * Setup the General tab fields
     * 
     * @since	0.11.0
     */
    public function card_oracle_setup_general_options()
    {
        $sections = array( array(
            'uid'   => 'general_section',
            'label' => __( 'General Settings', 'card-oracle' ),
            'page'  => 'card_oracle_option_general',
        ), array(
            'uid'   => 'email_section',
            'label' => __( 'Email Options', 'card-oracle' ),
            'page'  => 'card_oracle_option_general',
        ) );
        foreach ( $sections as $section ) {
            add_settings_section(
                $section['uid'],
                $section['label'],
                array( $this, 'card_oracle_section_callback' ),
                $section['page']
            );
        }
        $fields = array(
            array(
            'uid'          => 'card_oracle_powered_by',
            'label'        => __( 'Allow "Powered by"', 'card-oracle' ),
            'section'      => 'general_section',
            'type'         => 'checkbox',
            'options'      => false,
            'value'        => 'yes',
            'helper'       => __( 'Turn off to remove this from the footer', 'card-oracle' ),
            'supplemental' => __( '"Create your own card reading using Tarot Card Oracle! Go to ChilliChalli.com" is displayed in footer', 'card-oracle' ),
        ),
            array(
            'uid'     => 'card_oracle_allow_email',
            'label'   => __( 'Allow users to send reading to an email address', 'card-oracle' ),
            'section' => 'email_section',
            'type'    => 'checkbox',
            'options' => false,
            'value'   => 'yes',
        ),
            array(
            'uid'          => 'card_oracle_from_email',
            'label'        => __( 'From email address', 'card-oracle' ),
            'section'      => 'email_section',
            'type'         => 'text',
            'options'      => false,
            'placeholder'  => 'hello@example.com',
            'helper'       => __( 'The From email address used when the user sends the reading.', 'card-oracle' ),
            'supplemental' => __( 'If blank this defaults to the Admin email address.', 'card-oracle' ),
        ),
            array(
            'uid'          => 'card_oracle_from_email_name',
            'label'        => __( 'From email name', 'card-oracle' ),
            'section'      => 'email_section',
            'type'         => 'text',
            'options'      => false,
            'placeholder'  => __( 'Tarot Card Oracle', 'card-oracle' ),
            'helper'       => __( 'The Name displayed as the From email address.', 'card-oracle' ),
            'supplemental' => __( 'If blank this defaults to the site title.', 'card-oracle' ),
        ),
            array(
            'uid'          => 'card_oracle_email_text',
            'label'        => __( 'Text to display', 'card-oracle' ),
            'section'      => 'email_section',
            'type'         => 'text',
            'options'      => false,
            'placeholder'  => __( 'Email this Reading to:', 'card-oracle' ),
            'helper'       => __( 'Text to display on the email form.', 'card-oracle' ),
            'supplemental' => __( 'If blank this defaults "Email this Reading to:".', 'card-oracle' ),
        ),
            array(
            'uid'          => 'card_oracle_email_success',
            'label'        => __( 'Text to display', 'card-oracle' ),
            'section'      => 'email_section',
            'type'         => 'text',
            'options'      => false,
            'placeholder'  => __( 'Text to display on successful email:', 'card-oracle' ),
            'helper'       => __( 'Text to display after the user submits the email form.', 'card-oracle' ),
            'supplemental' => __( 'If blank this defaults "Your email has been sent. Please make sure to check your spam folder."', 'card-oracle' ),
        )
        );
        foreach ( $fields as $field ) {
            add_settings_field(
                $field['uid'],
                $field['label'],
                array( $this, 'card_oracle_option_callback' ),
                'card_oracle_option_general',
                $field['section'],
                $field
            );
            register_setting( 'card_oracle_option_general', $field['uid'] );
        }
    }
    
    /**
     * Callback function for Card Oracle Options
     * 
     * @since	0.9.0
     */
    public function card_oracle_option_callback( $arguments )
    {
        $value = get_option( $arguments['uid'] );
        
        if ( !$value && !empty($arguments['default']) ) {
            $value = $arguments['default'];
            // Set to our default
        }
        
        switch ( $arguments['type'] ) {
            case 'checkbox':
                
                if ( $value === "yes" ) {
                    $checked = 'checked="checked"';
                } else {
                    $checked = '';
                }
                
                printf(
                    '<label class="card-oracle-switch"><input name="%1$s" id="%1$s" type="%2$s"  
					value="%3$s" %4$s /><span class="card-oracle-slider round"></span></label>',
                    $arguments['uid'],
                    $arguments['type'],
                    $arguments['value'],
                    $checked
                );
                break;
            case 'text':
                printf(
                    '<input class="regular-text code" id="%1$s" name="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
                    $arguments['uid'],
                    $arguments['type'],
                    $arguments['placeholder'],
                    $value
                );
                break;
            case 'textarea':
                $rows = ( isset( $arguments['rows'] ) ? $arguments['rows'] : 3 );
                printf(
                    '<textarea class="large-text code" id="%1$s" name="%1$s" rows="%2$s" placeholder="%3$s">%4$s</textarea>',
                    $arguments['uid'],
                    $rows,
                    $arguments['placeholder'],
                    $value
                );
                break;
            case 'reading_list':
                echo  $this->get_reading_dropdown_box( $arguments['uid'], get_option( 'card_oracle_reading_list' ) ) ;
                break;
            case 'dropdown':
                // If it is a select dropdown
                
                if ( !empty($arguments['options']) && is_array( $arguments['options'] ) ) {
                    $options_markup = '';
                    foreach ( $arguments['options'] as $key => $label ) {
                        $options_markup .= sprintf(
                            '<option value="%s" %s>%s</option>',
                            $key,
                            selected( $value, $key, false ),
                            $label
                        );
                    }
                    printf( '<select name="%1$s" id="%1$s">%2$s</select>', $arguments['uid'], $options_markup );
                }
                
                break;
        }
        // If there is help text
        
        if ( !empty($arguments['helper']) ) {
            printf( '<span class="helper"> %s</span>', $arguments['helper'] );
            // Show it
        }
        
        // If there is supplemental text
        
        if ( !empty($arguments['supplemental']) ) {
            printf( '<p class="description">%s</p>', $arguments['supplemental'] );
            // Show it
        }
    
    }
    
    /**
     * Get all the posts of a custom post type, optional orderby and order
     * 
     * @since	0.7.0
     * @return	An array of all custom post_types requested
     */
    public function get_card_oracle_posts_by_cpt(
        $card_oracle_cpt,
        $order_by = 'ID',
        $order = 'ASC',
        $metakey = NULL,
        $metavalue = NULL,
        $metacompare = NULL
    )
    {
        $args = array(
            'numberposts' => -1,
            'order'       => $order,
            'orderby'     => $order_by,
            'post_status' => 'publish',
            'post_type'   => $card_oracle_cpt,
            'meta_query'  => array( array(
            'key'     => $metakey,
            'value'   => $metavalue,
            'compare' => $metacompare,
        ) ),
        );
        return get_posts( $args );
    }
    
    /**
     * Get all the descriptions post ids and content for a reading id and post_type co_descriptions
     * can include all cards when $card_id is not set or one or more cards when it is set. $card_id
     * can be a single id or an array of ids.
     * 
     * @since	0.5.0
     * @return	$description_ids	The array of description IDs and Content
     */
    public function get_co_description_id_content( $reading_id, $card_id = NULL )
    {
        global  $wpdb ;
        
        if ( isset( $card_id ) ) {
            $subquery = $card_id;
        } else {
            $subquery = "SELECT DISTINCT(id) FROM " . $wpdb->posts . " " . "INNER JOIN {$wpdb->postmeta} ON ID = post_id " . "WHERE post_type = 'co_cards' " . "AND post_status = 'publish' " . "AND meta_key = '_co_reading_id' " . "AND meta_value LIKE '%" . $reading_id . "%'";
        }
        
        $sql = "SELECT ID, post_content FROM " . $wpdb->posts . " " . "INNER JOIN {$wpdb->postmeta} ON ID = post_id " . "WHERE post_type = 'co_descriptions' " . "AND post_status = 'publish' " . "AND meta_key = '_co_card_id' " . "AND meta_value IN ('" . $subquery . "')";
        // The $positions is an array of all the positions in a reading, it consists of
        // the position title and position ID
        return $wpdb->get_results( $sql, OBJECT );
    }
    
    /**
     * Get all the descriptions post ids and reading id and post_type co_descriptions
     * 
     * @since	0.5.0
     * @return	$description_ids	The array of description IDs and Content
     */
    function get_co_description_ids( $reading_id )
    {
        global  $wpdb ;
        $sql = "SELECT ID FROM {$wpdb->posts} \r\n\t\t\tINNER JOIN {$wpdb->postmeta} m1 ON ID = m1.post_id\r\n\t\t\tINNER JOIN {$wpdb->postmeta} m2 ON ID = m2.post_id\r\n\t\t\tWHERE post_status = 'publish'\r\n\t\t\tAND ( m1.meta_key = '_co_card_id' AND m1.meta_value IN (\r\n\t\t\t\tSELECT ID FROM {$wpdb->posts}\r\n\t\t\t\tINNER JOIN {$wpdb->postmeta} ON ID = post_id \r\n\t\t\t\tWHERE meta_key = '_co_reading_id' AND meta_value LIKE '%" . $reading_id . "%' \r\n\t\t\t\tAND post_type = 'co_cards' AND post_status = 'publish' ) )\r\n\t\t\tAND ( m2.meta_key = '_co_position_id' AND m2.meta_value IN (\r\n\t\t\t\tSELECT ID FROM {$wpdb->posts}\r\n\t\t\t\tINNER JOIN {$wpdb->postmeta} ON ID = post_id \r\n\t\t\t\tWHERE meta_key = '_co_reading_id' AND meta_value LIKE '%" . $reading_id . "%'\r\n\t\t\t\tAND post_type = 'co_positions' AND post_status = 'publish' ) )";
        $description_ids = $wpdb->get_results( $sql, OBJECT );
        return $description_ids;
    }
    
    /**
     * Create our custom metabox for cards
     * 
     * @since	0.5.0
     * @return	void
     */
    public function get_reading_dropdown_box( $name, $selected_reading = NULL )
    {
        return wp_dropdown_pages( array(
            'echo'             => 0,
            'name'             => $name,
            'show_option_none' => __( 'Select a Reading:', 'card-oracle' ),
            'post_type'        => 'co_readings',
            'selected'         => $selected_reading,
            'sort_column'      => 'post_title',
        ) );
    }
    
    /**
     * Create our custom metabox for readings
     * 
     * @since	0.5.0
     * @return	void
     */
    public function add_meta_boxes_for_readings_cpt()
    {
        $screens = array( 'co_readings' );
        add_meta_box(
            'card-reading',
            __( 'Settings', 'card-oracle' ),
            array( $this, 'render_reading_metabox' ),
            $screens,
            'normal',
            'high'
        );
    }
    
    // add_meta_boxes_for_readings_cpt() {
    /**
     * Create our custom metabox for positions
     * 
     * @since	0.5.0
     * @return	void
     */
    public function add_meta_boxes_for_positions_cpt()
    {
        $screens = array( 'co_positions' );
        add_meta_box(
            'card-reading',
            __( 'Settings', 'card-oracle' ),
            array( $this, 'render_position_metabox' ),
            $screens,
            'normal',
            'high'
        );
    }
    
    // add_meta_boxes_for_positions_cpt() {
    /**
     * Create our custom metabox for cards
     * 
     * @since	0.11.0
     * @return	void
     */
    public function add_meta_boxes_for_cards_cpt()
    {
        $screens = array( 'co_cards' );
        add_meta_box(
            'card-reading',
            __( 'Settings', 'card-oracle' ),
            array( $this, 'render_card_metabox' ),
            $screens,
            'normal',
            'high'
        );
    }
    
    // add_meta_boxes_for_cards_cpt
    /**
     * Create our custom metabox for descriptions
     * 
     * @since	0.5.0
     * @return	void
     */
    public function add_meta_boxes_for_descriptions_cpt()
    {
        $screens = array( 'co_descriptions' );
        add_meta_box(
            'reverse',
            __( 'Reverse Card Description', 'card-oracle' ),
            array( $this, 'render_reverse_description_metabox' ),
            $screens,
            'normal',
            'high'
        );
        add_meta_box(
            'card',
            __( 'Settings', 'card-oracle' ),
            array( $this, 'render_description_metabox' ),
            $screens,
            'normal',
            'high'
        );
    }
    
    // add_meta_boxes_for_descriptions_cpt
    /**
     * Create our menu and submenus
     * 
     * @since	0.7.0
     * @return	void
     */
    public function card_oracle_menu_items()
    {
        // Card Oracle icon for admin menu svg
        $co_admin_icon = 'data:image/svg+xml;base64,' . base64_encode( '<svg height="100px" width="100px"  fill="black" 
			xmlns:x="http://ns.adobe.com/Extensibility/1.0/" 
			xmlns:i="http://ns.adobe.com/AdobeIllustrator/10.0/" 
			xmlns:graph="http://ns.adobe.com/Graphs/1.0/" 
			xmlns="http://www.w3.org/2000/svg" 
			xmlns:xlink="http://www.w3.org/1999/xlink" 
			version="1.1" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" 
			xml:space="preserve"><g><g i:extraneous="self">
			<circle fill="black" cx="49.926" cy="57.893" r="10.125"></circle>
			<path fill="black" d="M50,78.988c-19.872,0-35.541-16.789-36.198-17.503l-1.95-2.12l1.788-2.259c0.164-0.208,4.097-5.142,
				10.443-10.102 C32.664,40.296,41.626,36.751,50,36.751c8.374,0,17.336,3.546,25.918,10.253c6.346,4.96,10.278,9.894,
				10.443,10.102l1.788,2.259 l-1.95,2.12C85.541,62.2,69.872,78.988,50,78.988z M20.944,59.019C25.56,63.219,36.99,
				72.238,50,72.238 c13.059,0,24.457-9.013,29.061-13.214C74.565,54.226,63.054,43.501,50,43.501C36.951,43.501,25.444,
				54.218,20.944,59.019z"></path>
			<path fill="black" d="M44.305,30.939L50,21.075l5.695,9.864c3.002,0.427,6.045,1.185,9.102,2.265L50,7.575L35.203,33.204 
				C38.26,32.124,41.303,31.366,44.305,30.939z"></path>
			<path fill="black" d="M81.252,74.857L87.309,85H12.691l6.057-10.143c-2.029-1.279-3.894-2.629-5.578-3.887L1,92h98L86.83,
				70.97 C85.146,72.228,83.28,73.578,81.252,74.857z"></path>
			</g></g></svg>' );
        add_menu_page(
            __( 'Card Oracle', 'card-oracle' ),
            __( 'Card Oracle', 'card-oracle' ),
            'manage_options',
            'card-oracle-admin-menu',
            array( $this, 'display_card_oracle_options_page' ),
            $co_admin_icon,
            40
        );
        add_submenu_page(
            'card-oracle-admin-menu',
            __( 'Card Oracle Options', 'card-oracle' ),
            __( 'Dashboard', 'card-oracle' ),
            'manage_options',
            'card-oracle-admin-menu',
            array( $this, 'display_card_oracle_options_page' )
        );
        add_submenu_page(
            'card-oracle-admin-menu',
            __( 'Card Oracle Readings Admin', 'card-oracle' ),
            __( 'Readings', 'card-oracle' ),
            'manage_options',
            'edit.php?post_type=co_readings'
        );
        add_submenu_page(
            'card-oracle-admin-menu',
            __( 'Card Oracle positions Admin', 'card-oracle' ),
            __( 'Positions', 'card-oracle' ),
            'manage_options',
            'edit.php?post_type=co_positions'
        );
        add_submenu_page(
            'card-oracle-admin-menu',
            __( 'Card Oracle cards Admin', 'card-oracle' ),
            __( 'Cards', 'card-oracle' ),
            'manage_options',
            'edit.php?post_type=co_cards'
        );
        add_submenu_page(
            'card-oracle-admin-menu',
            __( 'Card Oracle Descriptions Admin', 'card-oracle' ),
            __( 'Descriptions', 'card-oracle' ),
            'manage_options',
            'edit.php?post_type=co_descriptions'
        );
        add_submenu_page(
            'card-oracle-admin-menu',
            __( 'Card Oracle Demo Data', 'card-oracle' ),
            __( 'Demo Data', 'card-oracle' ),
            'manage_options',
            'card-oracle-admin-demodata',
            array( $this, 'display_card_oracle_demodata_page' )
        );
    }
    
    /**
     * Move the featured image box for card readings
     * 
     * @since	0.5.0
     * @return	void
     */
    public function cpt_image_box()
    {
        // Move the image metabox from the sidebar to the normal position
        $screens = array( 'co_cards' );
        remove_meta_box( 'postimagediv', $screens, 'side' );
        add_meta_box(
            'postimagediv',
            __( 'Front of Card Image', 'card-oracle' ),
            'post_thumbnail_meta_box',
            $screens,
            'side',
            'default'
        );
        // Move the image metabox from the sidebar to the normal position
        $screens = array( 'co_readings' );
        remove_meta_box( 'postimagediv', $screens, 'side' );
        add_meta_box(
            'postimagediv',
            __( 'Back of Card Image', 'card-oracle' ),
            'post_thumbnail_meta_box',
            $screens,
            'side',
            'default'
        );
        //remove Astra metaboxes from our cpt
        $screens = array(
            'co_cards',
            'co_readings',
            'co_positions',
            'co_descriptions'
        );
        remove_meta_box( 'astra_settings_meta_box', $screens, 'side' );
        // Remove Astra Settings in Posts
        add_meta_box(
            'back-metabox',
            __( 'Previous Page', 'card-oracle' ),
            array( $this, 'render_back_button_metabox' ),
            $screens,
            'side',
            'high'
        );
    }
    
    /**
     * Display the custom admin columns for Cards
     * 
     * @since	0.7.0
     * @return	void
     */
    public function custom_card_column( $column )
    {
        global  $post ;
        global  $wpdb ;
        switch ( $column ) {
            case 'card_reading':
                $readings = get_post_meta( $post->ID, '_co_reading_id', false );
                foreach ( $readings as $reading ) {
                    echo  '<p>' ;
                    echo  get_the_title( $reading ) ;
                    echo  '</p>' ;
                }
                break;
            case 'card_order':
                echo  get_post_meta( $post->ID, '_co_card_order', true ) ;
                break;
            case 'co_shortcode':
                echo  '<input class="card-oracle-shortcode" id="copy' . $post->ID . '" value="[card-oracle id=&quot;' . $post->ID . '&quot;]"><button class="copyAction copy-action-btn button" value="[card-oracle id=&quot;' . $post->ID . '&quot;]"> <img src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'assets/images/clippy.svg" alt="Copy to clipboard"></button>' ;
                break;
            case 'description_reading':
                $position_id = get_post_meta( $post->ID, '_co_position_id', false );
                $reading_id = get_post_meta( $position_id, '_co_reading_id', false );
                echo  get_the_title( $reading_id[0] ) ;
                break;
            case 'number_card_descriptions':
                $reading_ids = get_post_meta( $post->ID, '_co_reading_id', false );
                foreach ( $reading_ids as $reading_id ) {
                    $count = count( $this->get_co_description_id_content( $reading_id, $post->ID ) );
                    $positions = count( $this->get_card_oracle_posts_by_cpt(
                        'co_positions',
                        NULL,
                        NULL,
                        '_co_reading_id',
                        $reading_id,
                        'LIKE'
                    ) );
                    
                    if ( $count == $positions ) {
                        echo  '<p>' . $count . '</p>' ;
                    } else {
                        echo  '<p><font color="red">' . $count . '</font></p>' ;
                    }
                
                }
                break;
            case 'number_reading_positions':
                echo  count( $this->get_card_oracle_posts_by_cpt(
                    'co_positions',
                    NULL,
                    NULL,
                    '_co_reading_id',
                    $post->ID,
                    'LIKE'
                ) ) ;
                break;
            case 'card_title':
                $card_id = get_post_meta( $post->ID, '_co_card_id', true );
                $card_title = get_the_title( $card_id );
                echo  '<strong><a class="row-title" href="' . admin_url() . 'post.php?post=' . $post->ID . '&action=edit">' . $card_title . '</a></strong>' ;
                break;
            case 'position_title':
                $position_id = get_post_meta( $post->ID, '_co_position_id', false );
                foreach ( $position_id as $id ) {
                    echo  '<p>' . get_the_title( $id ) . '</p>' ;
                }
                break;
            case 'position_number':
                $position_id = get_post_meta( $post->ID, '_co_position_id', false );
                foreach ( $position_id as $id ) {
                    echo  '<p>' . get_post_meta( $id, '_co_card_order', true ) . '</p>' ;
                }
                break;
        }
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since	0.5.0
     * @return	void
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Card_Oracle_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Card_Oracle_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/card-oracle-admin.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since	0.5.0
     * @return	void
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Card_Oracle_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Card_Oracle_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/card-oracle-admin.js',
            array( 'jquery' ),
            $this->version,
            false
        );
    }
    
    /**
     * Limit the number of Readings and Positions custom post type
     * 
     * @since	0.7.0
     * @return	void
     */
    public function limit_positions_cpt_count()
    {
        global  $typenow ;
        global  $action ;
        // If it's an edit don't block and return
        if ( $action === 'editpost' ) {
            return;
        }
        
        if ( $typenow === 'co_readings' ) {
            $message = __( 'Sorry, the maximum number of Readings has been reached.', 'card-oracle' );
            $limit = 1;
            // Grab all our Readings CPT
            $total = get_posts( array(
                'post_type'   => 'co_readings',
                'numberposts' => -1,
                'post_status' => 'publish, future, draft',
            ) );
        } elseif ( $typenow === 'co_positions' ) {
            $message = __( 'Sorry, the maxiumum number of Positions has been reached.', 'card-oracle' );
            $limit = 5;
            // Grab all our Positions CPT
            $total = get_posts( array(
                'post_type'   => 'co_positions',
                'numberposts' => -1,
                'post_status' => 'publish, future, draft',
            ) );
        } elseif ( $typenow === 'co_cards' ) {
            $message = __( 'Sorry, the maxiumum number of Cards has been reached.', 'card-oracle' );
            $limit = 25;
            // Grab all our Positions CPT
            $total = get_posts( array(
                'post_type'   => 'co_cards',
                'numberposts' => -1,
                'post_status' => 'publish, future, draft',
            ) );
        } else {
            return;
        }
        
        # Condition match, block new post
        
        if ( !empty($total) && count( $total ) >= $limit ) {
            echo  '<div class="notice notice-warning">' . $message . '</div>' ;
            echo  '<div class="notice notice-warning">' . __( 'Please consider upgrading to our premium version.', 'card-oracle' ) . '</div>' ;
            wp_die( __( 'Maximum reached', 'card-oracle' ), array(
                'response'  => 500,
                'back_link' => true,
            ) );
        }
    
    }
    
    /**
     * Create our custom post type for card readings
     * 
     * @since	0.5.0
     * @return	void
     */
    public function register_card_oracle_cpt()
    {
        // Register the Cards cpt
        // Set the labels for the custom post type
        $labels = array(
            'name'               => __( 'Cards', 'card-oracle' ),
            'singular_name'      => __( 'Card', 'card-oracle' ),
            'add_new'            => __( 'Add New Card', 'card-oracle' ),
            'add_new_item'       => __( 'Add New Card', 'card-oracle' ),
            'edit_item'          => __( 'Edit Card', 'card-oracle' ),
            'new_item'           => __( 'New Card', 'card-oracle' ),
            'all_items'          => __( 'All Cards', 'card-oracle' ),
            'view_item'          => __( 'View Card', 'card-oracle' ),
            'search_items'       => __( 'Search Cards', 'card-oracle' ),
            'featured_image'     => __( 'Card Image', 'card-oracle' ),
            'set_featured_image' => __( 'Add Card Image', 'card-oracle' ),
        );
        // Settings for our post type
        $args = array(
            'description'       => 'Holds our card information',
            'has_archive'       => false,
            'hierarchical'      => true,
            'labels'            => $labels,
            'menu_icon'         => 'dashicons-media-default',
            'menu_position'     => 42,
            'public'            => true,
            'show_in_menu'      => false,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => false,
            'supports'          => array( 'title', 'editor', 'thumbnail' ),
            'query_var'         => true,
        );
        register_post_type( 'co_cards', $args );
        // Register the Descriptions cpt
        // Set the labels for the custom post type
        $labels = array(
            'name'               => __( 'Card Descriptions', 'card-oracle' ),
            'singular_name'      => __( 'Card Description', 'card-oracle' ),
            'add_new'            => __( 'Add New Card Description', 'card-oracle' ),
            'add_new_item'       => __( 'Add New Card Description', 'card-oracle' ),
            'edit_item'          => __( 'Edit Card Description', 'card-oracle' ),
            'new_item'           => __( 'New Card Description', 'card-oracle' ),
            'all_items'          => __( 'All Card Descriptions', 'card-oracle' ),
            'view_item'          => __( 'View Card Description', 'card-oracle' ),
            'search_items'       => __( 'Search Card Descriptions', 'card-oracle' ),
            'featured_image'     => __( 'Card Description Image', 'card-oracle' ),
            'set_featured_image' => __( 'Add Card Description Image', 'card-oracle' ),
        );
        // Settings for our post type
        $args = array(
            'description'       => 'Holds our card description information',
            'has_archive'       => false,
            'hierarchical'      => true,
            'labels'            => $labels,
            'menu_icon'         => 'dashicons-format-gallery',
            'menu_position'     => 43,
            'public'            => true,
            'show_in_menu'      => false,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => false,
            'supports'          => array( 'title', 'editor' ),
            'query_var'         => true,
        );
        register_post_type( 'co_descriptions', $args );
        // register the Card Readings cpt
        // Set the labels for the custom post type
        $labels = array(
            'name'               => __( 'Card Readings', 'card-oracle' ),
            'singular_name'      => __( 'Card Reading', 'card-oracle' ),
            'add_new'            => __( 'Add New Card Reading', 'card-oracle' ),
            'add_new_item'       => __( 'Add New Card Reading', 'card-oracle' ),
            'edit_item'          => __( 'Edit Card Reading', 'card-oracle' ),
            'new_item'           => __( 'New Card Reading', 'card-oracle' ),
            'all_items'          => __( 'All Card Readings', 'card-oracle' ),
            'view_item'          => __( 'View Card Reading', 'card-oracle' ),
            'search_items'       => __( 'Search Card Readings', 'card-oracle' ),
            'featured_image'     => __( 'Card Back', 'card-oracle' ),
            'set_featured_image' => __( 'Add Card Back', 'card-oracle' ),
        );
        // Settings for our post type
        $args = array(
            'description'       => 'Holds our card reading information',
            'has_archive'       => false,
            'hierarchical'      => true,
            'labels'            => $labels,
            'menu_icon'         => 'dashicons-admin-page',
            'menu_position'     => 40,
            'public'            => true,
            'show_in_menu'      => false,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'supports'          => array( 'title', 'thumbnail' ),
            'query_var'         => true,
        );
        register_post_type( 'co_readings', $args );
        // Register the Positions cpt
        // Set the labels for the custom post type
        $labels = array(
            'name'               => __( 'Card Positions', 'card-oracle' ),
            'singular_name'      => __( 'Card Position', 'card-oracle' ),
            'add_new'            => __( 'Add New Card Position', 'card-oracle' ),
            'add_new_item'       => __( 'Add New Card Position', 'card-oracle' ),
            'edit_item'          => __( 'Edit Card Position', 'card-oracle' ),
            'new_item'           => __( 'New Card Position', 'card-oracle' ),
            'all_items'          => __( 'All Card Positions', 'card-oracle' ),
            'view_item'          => __( 'View Card Position', 'card-oracle' ),
            'search_items'       => __( 'Search Card Positions', 'card-oracle' ),
            'featured_image'     => __( 'Card Position Image', 'card-oracle' ),
            'set_featured_image' => __( 'Add Card Position Image', 'card-oracle' ),
        );
        // Settings for our post type
        $args = array(
            'description'       => 'Holds our card position information',
            'has_archive'       => false,
            'hierarchical'      => true,
            'labels'            => $labels,
            'menu_icon'         => 'dashicons-format-gallery',
            'menu_position'     => 41,
            'public'            => true,
            'show_in_menu'      => false,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => false,
            'supports'          => array( 'title' ),
            'query_var'         => true,
        );
        register_post_type( 'co_positions', $args );
    }
    
    // register_card_oracle_cpt
    /**
     * Create the admin settings for Card Oracle
     * 
     * @since	0.5.0
     * @return	void
     */
    public function register_card_oracle_admin_settings()
    {
        add_option( 'multi_positions_for_description', __( 'Allow multiple Positions for a Card Description.', 'card-oracle' ) );
        register_setting( 'card_oracle_options_group', 'multi_positions_for_description' );
    }
    
    /**
     * Render the Reading Metabox for Cards CPT
     * 
     * @since	0.7.0
     * @return	void
     */
    public function render_back_button_metabox()
    {
        global  $post_type ;
        $posttypes = get_post_types( array(
            'name' => $post_type,
        ), 'objects' );
        foreach ( $posttypes as $posttype ) {
            $page = $posttype->labels->name;
        }
        $text = __( 'Back to ', 'card-oracle' );
        echo  '<a href="' . 'edit.php?post_type=' . $post_type . '"<button class="button button-primary button-large">' . $text . $page . '</button></a>' ;
    }
    
    // End render_back_button_metabox
    /**
     * Render the Reading Metabox for Cards CPT
     * 
     * @since	0.5.0
     * @return	void
     */
    public function render_card_metabox()
    {
        global  $post ;
        // Generate nonce
        wp_nonce_field( 'meta_box_nonce', 'meta_box_nonce' );
        $readings = $this->get_card_oracle_posts_by_cpt( 'co_readings', 'post_title' );
        $selected_readings = get_post_meta( $post->ID, '_co_reading_id', false );
        echo  '<p class="card-oracle-metabox">' ;
        _e( 'Card Reading', 'card-oracle' );
        echo  '</p>' ;
        echo  '<div class="card-oracle-multiflex">' ;
        foreach ( $readings as $id ) {
            
            if ( is_array( $selected_readings ) && in_array( $id->ID, $selected_readings ) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = null;
            }
            
            echo  '<div class="card-oracle-multiitem"><input id="reading' . $id->ID . '" type="checkbox" 
				name="_co_reading_id[]" value="' . $id->ID . '" ' . $checked . ' />
				<label for="reading' . $id->ID . '">' . esc_html( $id->post_title ) . '</label></div>' ;
        }
        echo  '</div>' ;
    }
    
    // render_card_metabox
    /**
     * Render the Card Metabox for Descriptions CPT
     * 
     * @since	0.7.0
     * @return	void
     */
    public function render_description_metabox()
    {
        global  $post ;
        // Generate nonce
        wp_nonce_field( 'meta_box_nonce', 'meta_box_nonce' );
        $selected_card = get_post_meta( $post->ID, '_co_card_id', true );
        echo  '<p class="card-oracle-metabox">' ;
        _e( 'Card', 'card-oracle' );
        echo  '</p>' ;
        $dropdown = wp_dropdown_pages( array(
            'post_type'        => 'co_cards',
            'selected'         => $selected_card,
            'name'             => '_co_card_id',
            'show_option_none' => __( '(no card)', 'card-oracle' ),
            'sort_column'      => 'post_title',
            'echo'             => 0,
        ) );
        echo  $dropdown ;
        echo  '</p>' ;
        $selected_position = get_post_meta( $post->ID, '_co_position_id', false );
        echo  '<p class="card-oracle-metabox">' ;
        _e( 'Description Position', 'card-oracle' );
        echo  '</p>' ;
        $positions = $this->get_card_oracle_posts_by_cpt( 'co_positions', 'title', 'ASC' );
        echo  '<div class="card-oracle-multiflex">' ;
        foreach ( $positions as $id ) {
            $selected = get_post_meta( $post->ID, '_co_position_id', false );
            
            if ( is_array( $selected_position ) && in_array( $id->ID, $selected_position ) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = null;
            }
            
            echo  '<div class="card-oracle-multiitem"><input id="position' . $id->ID . '" class="card-oracle-multibox" type="checkbox" name="_co_position_id[]" value="' . $id->ID . '" ' . $checked . ' /><label for="position' . $id->ID . '">' . esc_html( get_the_title( $id->ID ) ) . '</label></div>' ;
        }
        echo  '</div>' ;
    }
    
    // render_description_metabox
    /**
     * Render the Reverse Descriptions Metabox for Descriptions CPT
     * 
     * @since	0.11.0
     * @return	void
     */
    public function render_reverse_description_metabox()
    {
        global  $post ;
        // Generate nonce
        wp_nonce_field( 'meta_box_nonce', 'meta_box_nonce' );
        $reverse_description = wpautop( get_post_meta( $post->ID, '_co_reverse_description', true ), true );
        wp_editor( $reverse_description, 'meta_content_editor', array(
            'wpautop'       => true,
            'media_buttons' => false,
            'textarea_name' => '_co_reverse_description',
            'textarea_rows' => 10,
            'teeny'         => true,
        ) );
    }
    
    /**
     * Render the Reading and Order Metabox for Positions CPT
     * 
     * @since	0.7.0
     * @return	void
     */
    public function render_position_metabox()
    {
        global  $post ;
        // Generate nonce
        wp_nonce_field( 'meta_box_nonce', 'meta_box_nonce' );
        $readings = $this->get_card_oracle_posts_by_cpt( 'co_readings', 'post_title' );
        $selected_readings = get_post_meta( $post->ID, '_co_reading_id', false );
        echo  '<p class="card-oracle-metabox">' ;
        _e( 'Card Reading', 'card-oracle' );
        echo  '</p>' ;
        echo  '<div class="card-oracle-multiflex">' ;
        foreach ( $readings as $id ) {
            
            if ( is_array( $selected_readings ) && in_array( $id->ID, $selected_readings ) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = null;
            }
            
            echo  '<div class="card-oracle-multiitem"><input id="reading' . $id->ID . '" type="checkbox" 
				name="_co_reading_id[]" value="' . $id->ID . '" ' . $checked . ' />
				<label for="reading' . $id->ID . '">' . esc_html( $id->post_title ) . '</label></div>' ;
        }
        echo  '</div>' ;
        echo  '<p><label class="card-oracle-metabox" for="_co_card_order">' ;
        _e( 'Card Order', 'card-oracle' );
        echo  '</label><br />' ;
        echo  '<input class="card-oracle-metabox-number" name="_co_card_order" type="number" min="1" ' . 'ondrop="return false" onpaste="return false" value="' . esc_html( $post->_co_card_order ) . '" /></p>' ;
    }
    
    // render_position_metabox
    /**
     * Render the Reading Metabox for Cards CPT
     * 
     * @since	0.11.0
     * @return	void
     */
    public function render_reading_metabox()
    {
        global  $post ;
        $settings = array(
            'wpautop'          => true,
            'media_buttons'    => false,
            'textarea_name'    => 'footer_text',
            'textarea_rows'    => 5,
            'tabindex'         => '',
            'editor_css'       => '',
            'editor_class'     => '',
            'teeny'            => false,
            'dfw'              => false,
            'tinymce'          => true,
            'quicktags'        => true,
            'drag_drop_upload' => false,
        );
        // Generate nonce
        wp_nonce_field( 'meta_box_nonce', 'meta_box_nonce' );
        // Get whether or not the Display Input box should be checked
        $display_question_checked = $post->display_question;
        if ( $display_question_checked === "yes" ) {
            $display_question_checked = 'checked="checked"';
        }
        echo  '<table class="card-oracle-reading-table">' ;
        echo  '<tr><th>' ;
        _e( 'Percentage of Cards that appear Reversed', 'card-oracle' );
        echo  '</th><td>' ;
        echo  '<input class="card-oracle-metabox-number" name="_co_reverse_percent" type="number" min="0" max="100"' . 'ondrop="return false" onpaste="return false" value="' . esc_html( $post->_co_reverse_percent ) . '" />' ;
        echo  '<label class="card-oracle-metabox" for="_co_reverse_percent">' ;
        _e( '%', 'card-oracle' );
        echo  '</label>' ;
        echo  '</td></tr>' ;
        echo  '<tr><th>' ;
        _e( 'Display Question Input Box', 'card-oracle' );
        echo  '</th><td>' ;
        echo  '<label class="card-oracle-switch">' ;
        echo  '<input type="checkbox" name="display_question" value="yes" ' . $display_question_checked . ' />' ;
        echo  '<span class="card-oracle-slider round"></span>' ;
        echo  '</label><p>' ;
        _e( 'Enabling this will display an input field to the users to enter a question.', 'card-oracle' );
        echo  '</p></td></tr>' ;
        echo  '<tr><th>' ;
        _e( 'Text for question input box', 'card-oracle' );
        echo  '</th><td>' ;
        echo  '<input class="card-oracle-metabox" id="question-text" name="question_text" type="text" value="' . wp_kses( get_post_meta( $post->ID, 'question_text', true ), array() ) . '" />' ;
        echo  '<p>' ;
        _e( 'Avoid using apostrophes in the text if you plan on allowing users to email the readings.', 'card-oracle' );
        echo  '</p></td></tr>' ;
        echo  '</table>' ;
        echo  '<p class="card-oracle-reading">' ;
        _e( 'Footer to be displayed on daily and random cards', 'card-oracle' );
        echo  '</p>' ;
        wp_editor( $post->footer_text, 'footer_text', $settings );
    }
    
    // render_reading_metabox
    /**
     * Save the card post meta for Card Oracle
     * 
     * @since	0.7.0
     * @return	void
     */
    public function save_card_oracle_meta_data()
    {
        global  $post ;
        if ( !$this->co_check_rights() ) {
            return;
        }
        // If the Card Reading ID has been selected then update it.
        
        if ( isset( $_POST['_co_reading_id'] ) || isset( $_POST['co_reading_dropdown'] ) ) {
            $current_readings = get_post_meta( $post->ID, '_co_reading_id', false );
            // Multiple Readings ID update
            
            if ( isset( $_POST['_co_reading_id'] ) ) {
                $post_readings = $_POST['_co_reading_id'];
                // If a current _co_position_id is not in the POST array then remove it.
                if ( !empty($current_readings) ) {
                    foreach ( $current_readings as $current_reading ) {
                        if ( !in_array( $current_reading, $post_readings ) ) {
                            delete_post_meta( $post->ID, '_co_reading_id', $current_reading );
                        }
                    }
                }
                // If a POST position is not in the current array then add it.
                if ( !empty($post_readings) ) {
                    foreach ( $post_readings as $reading ) {
                        if ( !in_array( $reading, $current_readings ) ) {
                            add_post_meta( $post->ID, '_co_reading_id', sanitize_text_field( $reading ) );
                        }
                    }
                }
                // Dropdown value update
            } elseif ( isset( $_POST['co_reading_dropdown'] ) ) {
                // If there is no value set remove the meta data otherwise update it
                
                if ( empty($_POST['co_reading_dropdown']) ) {
                    delete_post_meta( $post->ID, '_co_reading_id' );
                } else {
                    update_post_meta( $post->ID, '_co_reading_id', sanitize_text_field( $_POST['co_reading_dropdown'] ) );
                }
            
            }
        
        } else {
            delete_post_meta( $post->ID, '_co_reading_id' );
        }
        
        // If the Card Reading Display has been selected update it.
        
        if ( isset( $_POST['display_question'] ) ) {
            update_post_meta( $post->ID, 'display_question', sanitize_text_field( $_POST['display_question'] ) );
        } else {
            delete_post_meta( $post->ID, 'display_question' );
        }
        
        // If the Card Reading Display has been selected update it.
        
        if ( isset( $_POST['multiple_positions'] ) ) {
            update_post_meta( $post->ID, 'multiple_positions', sanitize_text_field( $_POST['multiple_positions'] ) );
        } else {
            delete_post_meta( $post->ID, 'multiple_positions' );
        }
        
        // If the Card Reading Footer text has been selected update it.
        
        if ( isset( $_POST['footer_text'] ) ) {
            update_post_meta( $post->ID, 'footer_text', wp_kses_post( $_POST['footer_text'] ) );
        } else {
            delete_post_meta( $post->ID, 'footer_text' );
        }
        
        // If the Card Reading Display has been selected update it.
        
        if ( isset( $_POST['question_text'] ) ) {
            update_post_meta( $post->ID, 'question_text', sanitize_text_field( $_POST['question_text'] ) );
        } else {
            delete_post_meta( $post->ID, 'question_text' );
        }
        
        // If the Card has been selected update it.
        
        if ( isset( $_POST['_co_card_id'] ) ) {
            update_post_meta( $post->ID, '_co_card_id', sanitize_text_field( $_POST['_co_card_id'] ) );
        } else {
            delete_post_meta( $post->ID, '_co_card_id' );
        }
        
        // If the Card has been selected update it.
        
        if ( isset( $_POST['_co_position_id'] ) ) {
            $current_positions = get_post_meta( $post->ID, '_co_position_id', false );
            // If a current _co_position_id is not in the POST array then remove it.
            foreach ( $current_positions as $current_position ) {
                if ( !in_array( $current_position, $_POST['_co_position_id'] ) ) {
                    delete_post_meta( $post->ID, '_co_position_id', $current_position );
                }
            }
            // If a current POST position is not in the current array then add it.
            foreach ( $_POST['_co_position_id'] as $position ) {
                if ( !in_array( $position, $current_positions ) ) {
                    add_post_meta( $post->ID, '_co_position_id', sanitize_text_field( $position ) );
                }
            }
        } else {
            delete_post_meta( $post->ID, '_co_position_id' );
        }
        
        // If the Card Position has been selected update it.
        
        if ( isset( $_POST['_co_card_order'] ) ) {
            update_post_meta( $post->ID, '_co_card_order', sanitize_text_field( $_POST['_co_card_order'] ) );
        } else {
            delete_post_meta( $post->ID, '_co_card_order' );
        }
        
        // If the Card Position has been selected update it.
        
        if ( isset( $_POST['_co_reverse_percent'] ) ) {
            update_post_meta( $post->ID, '_co_reverse_percent', sanitize_text_field( $_POST['_co_reverse_percent'] ) );
        } else {
            delete_post_meta( $post->ID, '_co_reverse_percent' );
        }
        
        // If the Reverse Card Description is set update it
        
        if ( isset( $_POST['_co_reverse_description'] ) ) {
            update_post_meta( $post->ID, '_co_reverse_description', wp_kses_post( $_POST['_co_reverse_description'] ) );
        } else {
            delete_post_meta( $post->ID, '_co_reverse_description' );
        }
    
    }
    
    /**
     * Set the admin columns for Cards
     * 
     * @since	0.5.0
     * @return	$columns 
     */
    public function set_custom_cards_columns( $columns )
    {
        // unset the date so we can move it to the end
        unset( $columns['date'] );
        $columns['card_reading'] = __( 'Associated Reading(s)', 'card-oracle' );
        $columns['number_card_descriptions'] = __( 'Number of Descriptions', 'card-oracle' );
        $columns['date'] = __( 'Date', 'card-oracle' );
        return $columns;
    }
    
    /**
     * Set the admin columns for Descriptions
     * 
     * @since	0.5.0
     * @return	void
     */
    public function set_custom_descriptions_columns( $columns )
    {
        // unset the date so we can move it to the end
        unset( $columns['date'] );
        $columns['card_title'] = __( 'Card', 'card-oracle' );
        //$columns['description_reading'] = __( 'Card Reading', 'card-oracle' );
        $columns['position_title'] = __( 'Position', 'card-oracle' );
        $columns['position_number'] = __( 'Position Number', 'card-oracle' );
        $columns['date'] = __( 'Date', 'card-oracle' );
        return $columns;
    }
    
    /**
     * Set the admin columns for Card Readings
     * 
     * @since	0.5.0
     * @return	$columns
     */
    public function set_custom_readings_columns( $columns )
    {
        // unset the date so we can move it to the end
        unset( $columns['date'] );
        $columns['co_shortcode'] = __( 'Shortcode', 'card-oracle' );
        $columns['number_reading_positions'] = __( 'Positions', 'card-oracle' );
        $columns['date'] = __( 'Date', 'card-oracle' );
        return $columns;
    }
    
    /**
     * Set the admin columns for Card Positions
     * 
     * @since	0.5.0
     * @return	$columns
     */
    public function set_custom_positions_columns( $columns )
    {
        // unset the date so we can move it to the end
        unset( $columns['date'] );
        $columns['card_reading'] = __( 'Card Reading', 'card-oracle' );
        $columns['card_order'] = __( 'Position', 'card-oracle' );
        $columns['date'] = __( 'Date', 'card-oracle' );
        return $columns;
    }
    
    /**
     * Set the sortable columns for Cards
     * 
     * @since	0.5.0
     * @return	$columns
     */
    public function set_custom_sortable_card_columns( $columns )
    {
        $columns['card_reading'] = 'card_reading';
        $columns['number_card_descriptions'] = 'number_card_descriptions';
        return $columns;
    }
    
    /**
     * Set the sortable columns for Descriptions
     * 
     * @since	0.5.0
     * @return	$columns
     */
    public function set_custom_sortable_description_columns( $columns )
    {
        $columns['card_title'] = 'card_title';
        $columns['description_reading'] = 'description_reading';
        $columns['position_title'] = 'position_title';
        $columns['position_number'] = 'position_number';
        return $columns;
    }
    
    /**
     * Set the sortable columns for Positions
     * 
     * @since	0.7.0
     * @return	$columns
     */
    public function set_custom_sortable_position_columns( $columns )
    {
        $columns['card_reading'] = 'card_reading';
        $columns['card_order'] = 'card_order';
        return $columns;
    }
    
    /**
     * Check the user has permissions
     * 
     * @since	0.5.0
     * @return	$columns
     */
    public function co_check_rights()
    {
        global  $post ;
        // Check nonce
        if ( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'meta_box_nonce' ) ) {
            return false;
        }
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return false;
        }
        // Prevent quick edit from clearing custom fields
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return false;
        }
        // Check the user's permissions.
        if ( !current_user_can( 'edit_post', $post->ID ) ) {
            return false;
        }
        return true;
    }
    
    private function card_oracle_feature_image( $post_id, $image_name )
    {
        $image = plugin_dir_path( __DIR__ ) . 'assets/images/' . $image_name;
        
        if ( file_exists( $image ) ) {
            
            if ( post_exists( $image_name ) ) {
                $page = get_page_by_title( $image_name, OBJECT, 'attachment' );
                $attachment_id = $page->ID;
                $destination = get_attached_file( $attachment_id );
                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $destination );
            } else {
                $upload = wp_upload_bits( $image_name, null, file_get_contents( $image, FILE_USE_INCLUDE_PATH ) );
                $file = $upload['file'];
                $file_type = wp_check_filetype( $file, null );
                // Attachment atrributes for the file
                $attachment = array(
                    'post_mime_type' => $file_type['type'],
                    'post_title'     => sanitize_file_name( $image_name ),
                    'post_content'   => get_the_title( $post_id ) . ' image.',
                    'post_status'    => 'inherit',
                );
                // Insert and return attachment id
                $attachment_id = wp_insert_attachment( $attachment, $file, $post_id );
                // Insert and return attachment metadata
                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $file );
            }
            
            // Update and return attachment metadata
            wp_update_attachment_metadata( $attachment_id, $attachment_data );
            // Update the Alt Text
            update_post_meta( $attachment_id, '_wp_attachment_image_alt', get_the_title( $post_id ) );
            // Associate the image to the post
            $success = set_post_thumbnail( $post_id, $attachment_id );
            return $success;
        }
    
    }
    
    function display_card_oracle_demodata_page()
    {
        $attributes = '';
        $i = 0;
        $json = file_get_contents( plugin_dir_url( dirname( __FILE__ ) ) . 'assets/data/demo-data.json' );
        $data_array = json_decode( $json, true );
        $positions = array();
        $readings = $this->get_card_oracle_posts_by_cpt( 'co_readings', 'post_title' );
        $reading_id = get_page_by_title( 'Past - Present - Future (Demo)', OBJECT, 'co_readings' );
        
        if ( isset( $reading_id ) ) {
            $this->card_oracle_write_log( 'Demo data already exists. Exiting.' );
            // The Reading already exists disable the button
            $attributes = 'disabled="disabled"';
            // Display admin notice
            echo  '<div class="notice notice-warning is-dismissible"><p>' ;
            _e( 'Appears you already have installed the demo data. To re-install you must delete the old data, and Empty Trash', 'card-oracle' );
            echo  '</p></div>' ;
        }
        
        // Check whether the button has been pressed AND also check the nonce
        
        if ( isset( $_POST['demo_data_button'] ) && check_admin_referer( 'demo_data_button_clicked' ) ) {
            $this->card_oracle_write_log( 'Inserting demo data.' );
            // the button has been pressed AND we've passed the security check
            // Insert the Reading
            $reading_id = wp_insert_post( array(
                'post_title'  => $data_array['reading']['name'],
                'post_type'   => 'co_readings',
                'post_status' => 'publish',
                'meta_input'  => array(
                '_co_reverse_percent' => $data_array['reading']['reversed'],
            ),
            ) );
            $this->card_oracle_feature_image( $reading_id, $data_array['reading']['image'] );
            // Insert the Past, Present, Future Positions
            foreach ( $data_array['positions'] as $position ) {
                $position_id = wp_insert_post( array(
                    'post_title'  => $position['name'],
                    'post_type'   => 'co_positions',
                    'post_status' => 'publish',
                    'meta_input'  => array(
                    '_co_reading_id' => $reading_id,
                    '_co_card_order' => $position['order'],
                ),
                ) );
                $positions[$position['name']] = $position_id;
            }
            // Insert the 22 major arcana Cards
            foreach ( $data_array['cards'] as $card ) {
                $card_id = wp_insert_post( array(
                    'post_title'   => $card['name'],
                    'post_type'    => 'co_cards',
                    'post_status'  => 'publish',
                    'post_content' => $card['description'],
                    'meta_input'   => array(
                    '_co_reading_id' => $reading_id,
                ),
                ) );
                $this->card_oracle_feature_image( $card_id, $card['image'] );
                // Insert a Description for each Card for each of the Positions
                foreach ( $positions as $key => $value ) {
                    wp_insert_post( array(
                        'post_title'   => $card['name'] . ' - ' . $key,
                        'post_type'    => 'co_descriptions',
                        'post_status'  => 'publish',
                        'post_content' => $card['upright'][0][$key],
                        'meta_input'   => array(
                        '_co_card_id'             => $card_id,
                        '_co_position_id'         => $value,
                        '_co_reverse_description' => $card['reverse'][0][$key],
                    ),
                    ) );
                }
            }
            // Just created the data so disable the button
            $attributes = 'disabled="disabled"';
            // Show success admin notice
            echo  '<div class="notice notice-success is-dismissible"><p>' ;
            _e( 'Demo data installed.', 'card-oracle' );
            echo  '</p></div>' ;
        }
        
        // Include the Card Oracle admin header file
        include_once 'partials/card-oracle-admin-header.php';
        echo  '<h2>' ;
        _e( 'Insert Demo Data for Tarot Card Oracle.', 'card-oracle' );
        echo  '</h2>' ;
        _e( 'This will create a set of demo for you consisting of:', 'card-oracle' );
        echo  '<ul class="ul-disc">' ;
        echo  '<li>' ;
        _e( 'One reading name "Past, Present, Future (Demo)"', 'card-oracle' );
        echo  '</li>' ;
        echo  '<li>' ;
        _e( 'Three Positions named "Past", "Present", "Future".', 'card-oracle' );
        echo  '</li>' ;
        echo  '<li>' ;
        _e( 'The 22 major arcana tarot cards.', 'card-oracle' );
        echo  '</li>' ;
        echo  '<li>' ;
        _e( '66 Descriptions, one for each of the 22 Card in each of the 3 Positions.', 'card-oracle' );
        echo  '</li>' ;
        echo  '</ul>' ;
        //echo '<form action="admin.php?page=card-oracle-admin-demodata" method="post">';
        echo  '<form action="" method="post">' ;
        // this is a WordPress security feature - see: https://codex.wordpress.org/WordPress_Nonces
        wp_nonce_field( 'demo_data_button_clicked' );
        echo  '<input type="hidden" value="true" name="demo_data_button" />' ;
        submit_button(
            'Insert Data',
            'primary',
            '',
            '',
            $attributes
        );
        echo  '</form>' ;
        // Include the Card Oracle admin footer file
        include_once 'partials/card-oracle-admin-footer.php';
    }

}
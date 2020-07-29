<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://cdgraham.com
 * @since      0.5.0
 *
 * @package    Card_Oracle
 * @subpackage Card_Oracle/public
 */
use  mailchimp\mailchimp ;
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Card_Oracle
 * @subpackage Card_Oracle/public
 * @author     Christopher Graham <chris@chillichalli.com>
 */
class Card_Oracle_Public
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
     * @since    0.5.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    0.5.0
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
            plugin_dir_url( __FILE__ ) . 'css/card-oracle-public.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    0.5.0
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
            plugin_dir_url( __FILE__ ) . 'js/card-oracle-public.js',
            array( 'jquery' ),
            $this->version,
            false
        );
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
     * Get all the published cards for a specific reading
     * 
     * @since	0.5.0
     * @return	void
     */
    public function get_cards_for_reading( $reading_id )
    {
        $args = array(
            'fields'      => 'ids',
            'numberposts' => -1,
            'post_type'   => 'co_cards',
            'post_status' => 'publish',
            'meta_query'  => array( array(
            'key'   => '_co_reading_id',
            'value' => $reading_id,
        ) ),
        );
        return get_posts( $args );
    }
    
    /**
     * Card Oracle sends an email with the reading via ajax
     * 
     * @since	0.11.0
     * @return	void
     */
    public function card_oracle_send_reading_email()
    {
        $body = '<style>' . wp_remote_retrieve_body( wp_remote_get( plugin_dir_url( __FILE__ ) . 'css/card-oracle-public-email.css' ) ) . '</style>';
        
        if ( isset( $_POST['email'] ) ) {
            //$site_title = get_bloginfo( 'name' );
            $to = sanitize_email( $_POST['email'] );
            $subject = get_option( 'card_oracle_email_subject', __( 'Your Reading', 'card-oracle' ) );
            $from_email_name = ( get_option( 'card_oracle_from_email_name' ) ?: get_bloginfo( 'name' ) );
            $from_email = '<' . (( get_option( 'card_oracle_from_email' ) ?: get_bloginfo( 'admin_email' ) )) . '>';
            // Create the headers. Add From name and address if options are set
            $headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $from_email_name . ' ' . $from_email );
            $body .= base64_decode( $_POST['emailcontent'] );
            wp_mail(
                $to,
                $subject,
                $body,
                $headers
            );
            $email_success = ( get_option( 'card_oracle_email_success' ) ? get_option( 'card_oracle_email_success' ) : __( 'Your email has been sent. Please make sure to check your spam folder.', 'card-oracle' ) );
            wp_send_json_success( $email_success, 'readingsend' );
        }
        
        wp_die();
    }
    
    /**
     * Card Oracle shortcode to display card reading
     * 
     * @since	0.11.0
     * @return	void
     */
    public function display_card_oracle_card_of_day( $atts )
    {
        // If the reading id is not set return
        
        if ( empty($atts['id']) ) {
            return;
        } else {
            $reading_id = $atts['id'];
        }
        
        
        if ( get_option( 'card_oracle_powered_by' ) ) {
            $powered_link = '<a href="https://chillichalli.com/card-oracle">ChilliChalli.com</a>';
            /* Translators %s is a website URL */
            $powered = sprintf( __( 'Create your own card reading using Tarot Card Oracle! Go to %s', 'card-oracle' ), $powered_link );
        }
        
        $card_ids = $this->get_cards_for_reading( $reading_id );
        $index = date( 'z' ) % max( count( $card_ids ), 1 );
        $card_of_day = get_post( $card_ids[$index] );
        $image = get_the_post_thumbnail( $card_of_day, 'medium' );
        $footer = get_post_meta( $reading_id, 'footer_text', true );
        
        if ( isset( $atts['email'] ) ) {
            $html = '<table class="card-oracle-table"><thead>';
            $html .= '<tr><th colspan="2"><h1>' . $card_of_day->post_title . '</h1></th></tr>';
            $html .= '</thead><tbody>';
            $html .= '<tr><td width="200" valign="top">' . $image . '</td><td style="vertical-align:top">' . apply_filters( 'the_content', $card_of_day->post_content ) . '</td></tr>';
            if ( !empty($footer) ) {
                $html .= '<tr colspan="2"><td>' . $footer . '</td></tr>';
            }
            if ( get_option( 'card_oracle_powered_by' ) ) {
                $html .= '<tr colspan="2"><td>' . $powered . '</td></tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $html = '<div class="cotd-wrapper">
				<cotd-header>' . $card_of_day->post_title . '</cotd-header>
				<cotd-main>' . $card_of_day->post_content . '</cotd-main>';
            if ( !empty($image) ) {
                $html .= '<cotd-aside>' . $image . '</cotd-aside>';
            }
            $html .= '<cotd-footer>';
            if ( !empty($footer) ) {
                $html .= $footer;
            }
            if ( get_option( 'card_oracle_powered_by' ) ) {
                
                if ( !empty($footer) ) {
                    $html .= '<br />' . $powered;
                } else {
                    $html .= $powered;
                }
            
            }
            $html .= '</cotd-footer>';
            $html .= '</div>';
        }
        
        return $html;
    }
    
    // End display_card_oracle_card_of_day
    /**
     * Card Oracle shortcode to display card reading
     * 
     * @since	0.11.0
     * @return	void
     */
    public function display_card_oracle_random_card( $atts )
    {
        // If the reading id is not set return
        
        if ( empty($atts['id']) ) {
            return;
        } else {
            $reading_id = $atts['id'];
        }
        
        $card_ids = $this->get_cards_for_reading( $reading_id );
        $card_count = count( $card_ids ) - 1;
        $card_of_day = get_post( $card_ids[rand( 0, $card_count )] );
        $image = get_the_post_thumbnail( $card_of_day, 'medium' );
        $footer = get_post_meta( $reading_id, 'footer_text', true );
        $display_html = '<div class="cotd-wrapper">
				<cotd-header>' . $card_of_day->post_title . '</cotd-header>
				<cotd-main>' . $card_of_day->post_content . '</cotd-main>';
        if ( !empty($image) ) {
            $display_html .= '<cotd-aside>' . $image . '</cotd-aside>';
        }
        $display_html .= '<cotd-footer>';
        if ( !empty($footer) ) {
            $display_html .= $footer;
        }
        
        if ( get_option( 'card_oracle_powered_by' ) ) {
            $powered_link = '<a href="https://chillichalli.com/card-oracle">ChilliChalli.com</a>';
            /* Translators %s is a website URL */
            $powered = sprintf( __( 'Create your own card reading using Tarot Card Oracle! Go to %s', 'card-oracle' ), $powered_link );
            
            if ( !empty($footer) ) {
                $display_html .= '<br />' . $powered;
            } else {
                $display_html .= $powered;
            }
        
        }
        
        $display_html .= '</cotd-footer>';
        $display_html .= '</div>';
        return $display_html;
    }
    
    // End display_card_oracle_random_card
    /**
     * Card Oracle shortcode to display card reading
     * 
     * @since	0.11.0
     * @return	void
     */
    public function display_card_oracle_set( $atts )
    {
        $page_display = '';
        // If the id is not set return
        
        if ( empty($atts['id']) ) {
            return;
        } else {
            $reading_id = $atts['id'];
        }
        
        // Check that the reading exists, if not return
        
        if ( 'publish' != get_post_status( $reading_id ) ) {
            $page_display = '<p>';
            $page_display .= __( 'This Reading is missing. Please contact the site administrator.', 'card-oracle' );
            $page_display .= '<p>';
            return $page_display;
        }
        
        // The $positions is an array of all the positions in a reading, it consists of
        // the position title and position ID
        $args = array(
            'numberposts' => -1,
            'order'       => 'ASC',
            'orderby'     => 'card_order_clause',
            'post_type'   => 'co_positions',
            'post_status' => 'publish',
            'meta_query'  => array(
            'reading_clause'    => array(
            'key'   => '_co_reading_id',
            'value' => $reading_id,
        ),
            'card_order_clause' => array(
            'key'  => '_co_card_order',
            'type' => 'numeric',
        ),
        ),
        );
        $positions = get_posts( $args );
        // Get the number of positions for this reading type.
        $positions_count = count( $positions );
        // Get the question text
        $question_text = get_post_meta( $reading_id, 'question_text', true );
        // Initial screen show question (if required) and backs of cards.
        
        if ( !isset( $_POST['Submit'] ) && !isset( $_POST['sendmail'] ) ) {
            // Get the image for the back of the card
            $card_back_img = get_the_post_thumbnail( $reading_id, 'medium' );
            if ( empty($card_back_img) ) {
                $card_back_img = '<img class="card-oracle-img-btn" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/cardback.png" alt="Back of Card">';
            }
            // Get all the published cards for this reading
            $card_ids = $this->get_cards_for_reading( $reading_id );
            // The number of cards returned
            $card_count = count( $card_ids );
            
            if ( $card_count != 0 ) {
                $i = 0;
                $percent = (int) get_post_meta( $reading_id, '_co_reverse_percent', true );
                foreach ( $card_ids as $card_id ) {
                    
                    if ( rand( 0, 100 ) < $percent ) {
                        $isUpright = 0;
                    } else {
                        $isUpright = 1;
                    }
                    
                    $new_card_ids[$i] = array(
                        'ID'      => $card_id,
                        'Upright' => $isUpright,
                    );
                    $i++;
                }
            }
            
            // Get just the card ids and shuffle them
            shuffle( $new_card_ids );
            // Display the form
            $page_display = '<div class="card-oracle-container">';
            $page_display .= '<div class="data" data-positions="' . $positions_count . '"></div>';
            $page_display .= '<form name="card-oracle-question" method="post">';
            if ( get_post_meta( $reading_id, 'display_question', true ) === "yes" ) {
                $page_display .= '<input name="question" id="question" type="text" size="40" placeholder="' . esc_attr( $question_text ) . '" required/>';
            }
            /* translators: %d is a number */
            $select_cards = esc_html( sprintf( _n(
                'Next select %d card.',
                'Next select %d cards.',
                $positions_count,
                'card-oracle'
            ), number_format_i18n( $positions_count ) ) );
            $page_display .= '<input name="picks" id="picks" type="hidden">
			<input name="reverse" id="reverse" type="hidden">
				<div class="btn-block">
					<button name="Submit" type="submit" id="Submit">Submit</button>
				</div>
			</form>
			<h2>' . $select_cards . '</h2>';
            // Display the back of the cards.
            $page_display .= '<div class="card-oracle-cards">';
            for ( $i = 0 ;  $i < $card_count ;  $i++ ) {
                $page_display .= '<div class="card-oracle-card">';
                $page_display .= '<div class="card-oracle-card-body">';
                $page_display .= '<div class="card-oracle-back">';
                
                if ( $new_card_ids[$i]['Upright'] == 1 ) {
                    $page_display .= '<button type="button" value="' . $new_card_ids[$i]['ID'] . '" id="id' . $new_card_ids[$i]['ID'] . '" onclick="this.disabled = true;" class="btn btn-default clicked">' . $card_back_img . '</button>';
                    $page_display .= '</div>';
                    $page_display .= '<div class="card-oracle-front">';
                    $page_display .= get_the_post_thumbnail( $new_card_ids[$i]['ID'] );
                } else {
                    $page_display .= '<button type="button" value="' . $new_card_ids[$i]['ID'] . '" id="id' . $new_card_ids[$i]['ID'] . '" data-value="' . $new_card_ids[$i]['ID'] . '" onclick="this.disabled = true;" class="btn btn-default clicked">' . $card_back_img . '</button>';
                    $page_display .= '</div>';
                    $page_display .= '<div class="card-oracle-front-reverse">';
                    $page_display .= get_the_post_thumbnail( $new_card_ids[$i]['ID'] );
                }
                
                $page_display .= '</div>';
                $page_display .= '</div>';
                $page_display .= '</div>';
            }
            $page_display .= '</div></div>';
        }
        
        // ! isset( $_POST['Submit'] )
        // Post submitted display cards and descriptions.
        
        if ( isset( $_POST['Submit'] ) ) {
            $cards = explode( ',', $_POST['picks'] );
            $reverse_cards = explode( ',', $_POST['reverse'] );
            $description_content = '';
            $email_body = '<table class="card-oracle-table"><thead>';
            $form_text = get_option( 'card_oracle_email_text', __( 'Email this Reading to:', 'card-oracle' ) );
            $page_display = '<div class="wrap">';
            
            if ( !empty($_POST["question"]) ) {
                $page_display .= '<h2>' . $question_text . '</h2><h3>' . sanitize_text_field( $_POST["question"] ) . '</h3>';
                $email_body .= '<tr><th><h1>' . sanitize_text_field( $_POST["question"] ) . '</h1></th></tr>';
            }
            
            $email_body .= '</thead><tbody>';
            for ( $i = 0 ;  $i < count( $cards ) ;  $i++ ) {
                $args = array(
                    'post_type'   => 'co_descriptions',
                    'post_status' => 'publish',
                    'meta_query'  => array( array(
                    'key'   => '_co_card_id',
                    'value' => $cards[$i],
                ), array(
                    'key'   => '_co_position_id',
                    'value' => $positions[$i]->ID,
                ) ),
                );
                $description_id = get_posts( $args );
                $image = get_the_post_thumbnail( $cards[$i] );
                $reverse_class = ( in_array( $cards[$i], $reverse_cards, true ) ? 'class="card-oracle-rotate-image"' : '' );
                // Add the Image, Card title, and the Position description to the email
                $email_body .= '<tr><td colspan="2"><center><h2>' . $positions[$i]->post_title . '</h2></center></td></tr>';
                $email_body .= '<tr><td width="200" rowspan="2" valign="top" ' . $reverse_class . '>' . $image . '</td>';
                
                if ( $reverse_class ) {
                    $email_body .= '<td><h3>' . get_the_title( $cards[$i] ) . ' (Reversed)</h3></td></tr>';
                } else {
                    $email_body .= '<td><h3>' . get_the_title( $cards[$i] ) . '</h3></td></tr>';
                }
                
                // If there is a description add it to the page and email otherwise skip it
                
                if ( $description_id ) {
                    $description_content = apply_filters( 'the_content', ( $reverse_class ? $description_id[0]->_co_reverse_description : $description_id[0]->post_content ) );
                    $main_text = '<cotd-main><h3>' . get_the_title( $cards[$i] ) . ' (' . $cards[$i] . ')' . '</h3>' . $description_content . '</cotd-main>';
                    $email_body .= '<tr><td style="vertical-align:top">' . $description_content . '</td></tr><tr height="20"></tr>';
                } else {
                    $main_text = '<cotd-main><h3>' . get_the_title( $cards[$i] ) . ' (' . $cards[$i] . ')' . '</h3></cotd-main>';
                    $email_body .= '<tr><td style="vertical-align:top"></td></tr><tr height="20"></tr>';
                }
                
                // Add the Image, Card title, and the Position description to the page
                $page_display .= '<div class="cotd-wrapper"><cotd-header>' . $positions[$i]->post_title . '</cotd-header><cotd-aside ' . $reverse_class . '>' . $image . '</cotd-aside>' . $main_text . '</div>';
            }
            $page_display .= '</div>';
            $email_body .= '</tbody></table>';
            // Add email button to page if option enabled
            
            if ( get_option( 'card_oracle_allow_email' ) ) {
                $page_display .= '<div class="card-oracle-email">';
                $page_display .= '<p>' . $form_text . '</p>';
                $page_display .= '<input type="text" name="emailaddress" placeholder="' . esc_attr__( 'Email Address', 'card-oracle' ) . '" id="emailaddress" />';
                $page_display .= '<input type="submit" name="reading-send" value="Send" id="reading-send" />';
                $page_display .= '<input type="hidden" id="ajax_url" name="ajax_url" value="' . admin_url( 'admin-ajax.php' ) . '">';
                $page_display .= '<input type="hidden" id="emailcontent" name="emailcontent" value="' . base64_encode( $email_body ) . '">';
                
                if ( get_option( 'card_oracle_subscribe' ) ) {
                    $page_display .= '<br /><div class="card-oracle-subscribe">';
                    $page_display .= '<input type="checkbox" id="card-oracle-subscribe" name="card-oracle-subscribe"' . 'value="false" />';
                    
                    if ( get_option( 'card_oracle_email_consent_text' ) ) {
                        $text = get_option( 'card_oracle_email_consent_text' );
                    } else {
                        $text = __( 'Subscribe to our list to keep up to date with our lasted news.', 'card-oracle' );
                    }
                    
                    $page_display .= '<label for="card-oracle-subscribe">' . $text . '</label>';
                    $page_display .= '</div>';
                }
                
                $page_display .= '<p class="card-oracle-response"></p>';
                $page_display .= '</div>';
            }
        
        }
        
        // End POST submit
        return $page_display;
    }

}
// End Class Card_Oracle_Public
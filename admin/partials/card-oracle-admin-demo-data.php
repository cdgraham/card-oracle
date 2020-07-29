<?php

namespace demodata;

/**
 * Super-simple, minimum abstraction MailChimp API v3 wrapper
 *  *
 * @author  Christopher Graham
 * @version 1.0
 */
class demodata
{

    private function card_oracle_import_json_data() {
        $json = file_get_contents( plugin_dir_url( dirname( __FILE__ ) ) . 'assets/data/demo-data.json' );
        $data_array = json_decode( $json, true );
        
        return $data_array;
    }
}
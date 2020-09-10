<?php

namespace YIKES\EasyForms\Util;

use YIKES\EasyForms\Service;

class Debugger implements Service {

    const LOG_NAME = 'freddie_log';

    public function register() {
        $this->register_log();
    }

    public function register_log() :void {
        if ( ! get_option( self::LOG_NAME ) ) {
            add_option( self::LOG_NAME, [] );
        }
    }

    public function get_log() {
        return get_option( self::LOG_NAME, [] );
    }

   public function pretty_log() {
       $log = $this->get_log();
       return $this->pretty_debug( 'Pretty Log', $log );
   }

    public function pretty_debug( $label, $value ) {
            $res = "<strong>{$label}</strong>";
            $res .= "<pre>";
            $res .= esc_html( json_encode( $value, JSON_PRETTY_PRINT ) );
            $res .= "</pre>";
            echo $res;
    }
}
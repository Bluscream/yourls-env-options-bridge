<?php
/*
Plugin Name: Environment Options Bridge
Plugin URI: https://github.com/yourls/env-options-bridge
Description: Dynamically overrides any option via environment variables starting with YOURLS_OPTION_ (e.g. YOURLS_OPTION_PS_TITLE overrides ps_title).
Version: 1.0
Author: Antigravity.AI
*/

if ( !defined( 'YOURLS_ABSPATH' ) ) die();

// We can register filters dynamically on plugins_loaded for any options we want to support
yourls_add_action( 'plugins_loaded', 'env_options_bridge_init' );

function env_options_bridge_init() {
    // List of option keys we want to check for env overrides
    $options_to_bridge = [
        'ps_site_key',
        'ps_secret_key',
        'ps_title',
        'ps_subtitle',
        'ps_bg_start',
        'ps_bg_end',
        'ps_text_primary',
        'ps_text_secondary',
        'ps_accent',
        'ps_accent_hover',
        'ozh_yourls_gsb',
        'youtube_title_fix_api_key',
        'fallback_url',
        'random_shorturls_length',
        'logo_suite_image_url',
        'logo_suite_image_alt',
        'logo_suite_image_title',
        'logo_suite_custom_title',
    ];

    foreach ( $options_to_bridge as $option ) {
        yourls_add_filter( 'shunt_option_' . $option, function( $value ) use ( $option ) {
            // Check for direct uppercase env var (e.g. PS_TITLE)
            $env_var_direct = strtoupper( $option );
            $env_val = getenv( $env_var_direct );
            if ( $env_val !== false && $env_val !== '' ) {
                return $env_val;
            }

            // Also support a prefix namespace: YOURLS_OPTION_PS_TITLE
            $env_var_prefixed = 'YOURLS_OPTION_' . strtoupper( $option );
            $env_val = getenv( $env_var_prefixed );
            if ( $env_val !== false && $env_val !== '' ) {
                return $env_val;
            }

            // Return shunt_default if not overridden, so core query proceeds normally
            return yourls_shunt_default();
        } );
    }
}

<?php
/*
Plugin Name: Environment Options Bridge
Plugin URI: https://github.com/yourls/env-options-bridge
Description: Dynamically overrides any YOURLS option via environment variables (e.g. env var PS_TITLE overrides option ps_title).
Version: 2.0
Author: Antigravity.AI
*/

if ( !defined( 'YOURLS_ABSPATH' ) ) die();

class EnvOptionsBridgeArray extends ArrayObject {
    public function offsetExists($key): bool {
        $exists = parent::offsetExists($key);
        if (!$exists && strpos($key, 'shunt_option_') === 0) {
            return true; // Pretend the filter exists so YOURLS runs it
        }
        return $exists;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($key) {
        $val = parent::offsetExists($key) ? parent::offsetGet($key) : null;
        if (strpos($key, 'shunt_option_') === 0) {
            $option_name = substr($key, 13); // Extract option name after 'shunt_option_'
            
            // Generate direct uppercase env var name (e.g. ps_title -> PS_TITLE)
            $env_var = strtoupper($option_name);
            
            // Only intercept if the env var is set and non-empty
            $env_val = getenv($env_var);
            if ($env_val !== false && $env_val !== '') {
                // Return a structure matching $yourls_filters hook format to hook in our override
                $bridge_filter = [
                    10 => [
                        'env_options_bridge_override' => [
                            'function' => function($value) use ($env_val) {
                                return $env_val;
                            },
                            'accepted_args' => 1,
                            'type' => 'filter'
                        ]
                    ]
                ];
                
                if (is_array($val)) {
                    return $bridge_filter + $val;
                }
                return $bridge_filter;
            }
        }
        return $val;
    }
}

// Replace the global filters array with our custom interceptor
global $yourls_filters;
if (is_array($yourls_filters)) {
    $yourls_filters = new EnvOptionsBridgeArray($yourls_filters);
} elseif ($yourls_filters instanceof ArrayObject) {
    // If already an ArrayObject, copy its contents
    $yourls_filters = new EnvOptionsBridgeArray($yourls_filters->getArrayCopy());
} else {
    $yourls_filters = new EnvOptionsBridgeArray();
}

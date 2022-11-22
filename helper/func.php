<?php

if (!function_exists('is_testing')) {

    /**
     * Check environment mode
     *
     * @return boolean
     */
    function is_testing() {
        return config('app.env') == 'testing';
    }
}


if (!function_exists('is_jit_enabled')) {
    function is_jit_enabled(): bool {
        if (!function_exists('opcache_get_status')) {
            return false;
        }
    
        return !empty(opcache_get_status()['jit']['enabled']);
    }
}

if (!function_exists('is_opcache_enabled')) {


    function is_opcache_enabled(): bool {
        if (!function_exists('opcache_get_status')) {
            return false;
        }
    
        return !empty(opcache_get_status()['opcache_enabled']);
    }
}
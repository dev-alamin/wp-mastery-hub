<?php

namespace WPMASTERYHUB;

use WPMASTERYHUB\Admin\Menu;

/**
 * Handles admin functionality for the plugin.
 * Initializes and dispatches all admin-related classes.
 */
class Admin {

    public function __construct()
    {
        (new Menu())->register_hooks();
    }
}
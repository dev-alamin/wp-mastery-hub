<?php 

namespace WPMASTERYHUB;

use WPMASTERYHUB\RestAPI\Controllers\BookController;

class WP_Core {
    public function __construct()
    {
        (new CPT())->register_hooks();
        (new Taxonomy())->register_hooks();
        (new BookController())->register_hooks();
    }
}
<?php
/*
Plugin Name: Creative Commons Credits
Description: Plugin adds a meta box to enter all data needed for a propper Creative Commons Attribution. If data is provided, attribution gets appended to the content.
Author: Bego Mario Garde
Author URI: https://garde-medienberatung.de
Version: 1.0
License: GPL2
Text Domain: cc-credits
Domain Path: /languages
*/

/*

    Copyright (C) 2015  Bego Mario Garde <begomario.garde@gmx.de>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;


// INCLUDE CLASS
require plugin_dir_path( __FILE__ ) . 'class-cc-credits.php';


$plugin = new CC_Credits();

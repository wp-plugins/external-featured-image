<?php
/**
 * Copyright 2013 Nelio Software S.L.
 * This script is distributed under the terms of the GNU General Public
 * License.
 *
 * This script is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License. This script is
 * distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/>.
 */


/*
 * Plugin Name: Nelio External Featured Image
 * Description: Use external images as your posts/page featured images. If you use an external service for storing your images and then linking them from your WordPress site, this plugin will perfectly fit your needs.
 * Version: 1.2.0
 * Author: Nelio Software
 * Plugin URI: http://neliosoftware.com
 * Text Domain: nelioefi
 */

// ==========================================================================
// PLUGIN INFORMATION
// ==========================================================================
	define( 'NELIOEFI_PLUGIN_VERSION', '1.2.0' );
	define( 'NELIOEFI_PLUGIN_NAME', 'Nelio External Featured Image' );
	define( 'NELIOEFI_PLUGIN_DIR_NAME', basename( dirname( __FILE__ ) ) );

// Defining a few important directories
	define( 'NELIOEFI_ROOT_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
	define( 'NELIOEFI_DIR', NELIOEFI_ROOT_DIR . '/includes' );
	define( 'NELIOEFI_ADMIN_DIR', NELIOEFI_DIR . '/admin' );

// Some URLs...
	define( 'NELIOEFI_ASSETS_URL', plugins_url() . '/' . NELIOEFI_PLUGIN_DIR_NAME . '/assets' );


// REGULAR STUFF
	require_once( NELIOEFI_DIR . '/nelio-efi-main.php' );

// ADMIN STUFF
	if ( is_admin() ) {
		require_once( NELIOEFI_ADMIN_DIR . '/edit-post.php' );
		require_once( NELIOEFI_ADMIN_DIR . '/nelioab-campaign.php' );
	}


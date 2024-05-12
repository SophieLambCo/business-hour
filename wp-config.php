<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u674604570_oUho6' );

/** Database username */
define( 'DB_USER', 'u674604570_YKcng' );

/** Database password */
define( 'DB_PASSWORD', 'ERq1Inn24Q' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '!}3.fhY_H>hT($v$@2tZ$P3mA06[nUt5u:xGmeU>mL@p/6zr33Amb#vDonj`> ~/' );
define( 'SECURE_AUTH_KEY',   '*r 8rg_uK0+?>(T)y3^O.EC0Nm[g7*{9hxW.UIUn&_N^n:!^:c 2h@H/AwwuW~ue' );
define( 'LOGGED_IN_KEY',     'LS:D[*~~QStFQ&}6nY[b2:pN%:mxC`bpl}dOb/bV]6mXi?!v<N#h|5L=*0mu0TE|' );
define( 'NONCE_KEY',         'Pyp:rx~c,&eT=oDLqk{4s=Mz28H<?BsI&_U{f>4#vQlwoExw_72X9gb_!:9w/|NV' );
define( 'AUTH_SALT',         'Ru9cSzO~H;*NX=_i_Bwh^qPy,=fX$m@MJKI!V5E|@,3+#]]!Z@#wi~YsZh#J3-h,' );
define( 'SECURE_AUTH_SALT',  '-FW7v)6=<})I-$?dyhz%=.^rLP!l^IW`qjD8)FPc!R[jPqnk-j^|ACaZa`E64V$o' );
define( 'LOGGED_IN_SALT',    'ZY,W?U9l|r@v[z$ROXw^gL0_,roRs-5dm;Yv>b[R|@J`A5@N;[fWJEet.>jwbqDJ' );
define( 'NONCE_SALT',        '|}#(bqy)RCpPxZ6rLEHpA+G0o$W:V?^bvO7rS.x7SLY1.|Zh|D>>FDH6=}izOyu2' );
define( 'WP_CACHE_KEY_SALT', 'Z.dbaG~,V<=)`37JSPdYBU%[=Z%3akgHtU;LDIbC.&#~X]}xm.~bfhvWP5hi[h~<' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */

define( 'WP_DEBUG', true );


/* Add any custom values between this line and the "stop editing" line. */
define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

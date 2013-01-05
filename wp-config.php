<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'nomicly_dev');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '`pK8B5g9}T<@nTZv9q82Q>+m&gSm+`Ub-Rhv7hnrrJ f$+>O}%&8Y(;hPkl1iXe1');
define('SECURE_AUTH_KEY',  'P/(NEQ$~)MtF>`yQ|<1qp[w_vUgm(20;RZbN}8)vZV`u_d88+a$||1J1eN3LTAqB');
define('LOGGED_IN_KEY',    ']+#a(9-~mMZ kr2z2^4!:xhp:]F ^i0Y-(4tm]EK82JJ02dN,Z4K/sxVmigDlix@');
define('NONCE_KEY',        'm!d48[&}#({~}n-oP`g|x:R5E?lQ|x09jl}[_+|2ke-1M?JF+ydOn]*}a26E`j-0');
define('AUTH_SALT',        '1h?Z9+~#Z*F[Ii#Qa]_#.4R|sD0ZuuM%D#I-!cToILfo$)R6kn0$gHxVuXdgI|OT');
define('SECURE_AUTH_SALT', '!O9)[poMKt.I[6mEy5Dt}[u[d24wYB8w7%.YEn1An6!Y-X|;g>.nNVD$l|loV}|X');
define('LOGGED_IN_SALT',   ']Rre5&C;-u8X&)Xd5M$];{fcGxC{M>fC%$Y)1O[1`2}E@0e!:24|!XV}tf-qG,(*');
define('NONCE_SALT',       '!_58LN_ Idg+]Y!e3ti{zv WWD&qEMs|[:ii x@goQ>UI4GIc %5yi{&H,ML-+uv');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'nomicly_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

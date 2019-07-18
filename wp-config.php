<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ']v4CLffTIm[Fm>aJ%jx<PJ]Zmj.=0X.j?xJF,fq+woDm`IoSdr%G,.g)gZuC.A1V' );
define( 'SECURE_AUTH_KEY',  'GS=hVLi=O*x}xhQ#=MOU_o K05vRrx/WU@uJ#xwvmJCn3hP?JN/RbY#+qVHc(~yC' );
define( 'LOGGED_IN_KEY',    'X$LIglH5}iC92qeV.nXaD>nl?[2DHYu^pGcAC$>6Hv!N$GK5R79d:^#}a|vfD~]@' );
define( 'NONCE_KEY',        'fE!C)^Ahl!i#X5aW0kI1MR2Dy>[O5&NT Qg94wbG3(9)3/ak5nzW>Q(}H0 #W6(Y' );
define( 'AUTH_SALT',        '$.. |f<xB4ms0/p[wb)_n)KH<(?/cdjhF[{0l:tdL)M^`l}Hn6t`pr3+;xa#,8B9' );
define( 'SECURE_AUTH_SALT', 'EpV=>#tDs|pm4G UA W;cOEbM~O,P^<efqh{aG|iLb$JY|q05WzRa!ZZU1{l]#)D' );
define( 'LOGGED_IN_SALT',   '^oy-^!Pz(a8Y#Ohm<m]% T`J4wwl;:W&OEM(:**-i>]:rvtw&oQ7!8{D$3N=B)|O' );
define( 'NONCE_SALT',       'ViXm>PC^&xh(M{^%4VM8WIE}6;<u0vz0D.s2jlK59()i(ndACsaymM~W+cifaj53' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );

set_time_limit(300);
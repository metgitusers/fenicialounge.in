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
define( 'DB_NAME', 'fenicialounge_fen' );
 
/** MySQL database username */
define( 'DB_USER', 'fenicialounge_fen' );

/** MySQL database password */
define( 'DB_PASSWORD', '#B{%cA[!8GbJ' );

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
define( 'AUTH_KEY',         '+$y9YQH5iqBbj5:j5FzA!pscwFsLf Fqd;r $!%<UD:Nb*T.D3fd6Hp=>Cf*)leY' );
define( 'SECURE_AUTH_KEY',  'ca<tb9R.D *+$a1z[^X;&C0Y/:gf0N<1LgB4#U@e#EK4KSH`2pN<zF(xuWyA~5ms' );
define( 'LOGGED_IN_KEY',    'R*R!>6gl{bje7<CBxt[uk52|~sf)G>3plT8)2[V2Eg!nar6>sGF)cx>J&A^spHdG' );
define( 'NONCE_KEY',        'm|0Pg_8qA4X+x3UOWBWB#{:)wP)I1|PvC{Y;qmU=Alx!t#J`:l~a=i<$JE:Wv0~F' );
define( 'AUTH_SALT',        't$VO)tneMN1fja]<RNgtgW$-llm+E(9fg(GeZG<$A2X~Z?Y=/+WMyI.0kRjja<Km' );
define( 'SECURE_AUTH_SALT', 'cHR`i:U&!}$6``QvZ/saeW*Jn#8~C:RIYho}SD*$k9MoK8I0%~PM}9ZI%~vn.Tp3' );
define( 'LOGGED_IN_SALT',   'A/oyM!Ms6O_O2SVi}HpaA(+eX-ZG5kHj!&(x{-dc#1N]SeI-$j3MBXFJsBk)hGPy' );
define( 'NONCE_SALT',       'a.@Y$Is]/#yS@B9sH<(Mo?&;v1sLM(e1S+F5g?GZnq=)*&uZ|>!Kw<G9fo;92:I^' );

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

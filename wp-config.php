<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'osd_db');

/** MySQL database username */
define('DB_USER', 'osd_user');

/** MySQL database password */
define('DB_PASSWORD', 'osd_pass');

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
 */
define('AUTH_KEY',         'D!!l .1EDl(.2,,o-hH4!/l#1ms*>&zg+-,g/i]E~rb=p#}+-HF$`]Z^w-pM|C.Y');
define('SECURE_AUTH_KEY',  '+OOHKs5B_*.i3M! aE7d]V)I] cjB1TY,TY+WWYo^z.ROIR2(^Gq)HDL2zStI@KW');
define('LOGGED_IN_KEY',    'Yg@+J9Gr_DUN]+Zr#!;Pm{;mj9z|=gqc77>l+n+U/U||^2Qyjn=?Xu6M}J*z`;gu');
define('NONCE_KEY',        'P)z2{/Pt#!8c5}%[ng jJ}:ch}.zy8hMx5B5h);JI/;pt:JFDZNkMS.R;T9,I&Ds');
define('AUTH_SALT',        ']F~ead)Lm*-Bi>cJlzAM^FGB[xdn,tV(+XhGO+^/%v-h)]qstHs+mj8+)J?`B|(.');
define('SECURE_AUTH_SALT', 'h?B6_q|A3G4O-EE0^=ZRE{rn^QwxHQxh|Oq4@-f~9D$.6xw`q3&do,CQ@;Y?#|!j');
define('LOGGED_IN_SALT',   ')~*|@EMh#3& Q+VPOXM++.j3lQ&`_t_`vaKyX<yE%b+ar9CL.^VW42<G3C{k3h,<');
define('NONCE_SALT',       'spa7)y8E~.({v9TX#b(--~dIHz1$Ad0.kG7%!r}<,=n(Y_d)bCYA`&{Yj_OcYD|Y');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'osd_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', 'da_DK');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
$base = '/';
define('DOMAIN_CURRENT_SITE', 'openswimdata.dev');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

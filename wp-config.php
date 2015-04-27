<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'stacymar_skm');

/** MySQL database username */
define('DB_USER', 'stacymar_skm');

/** MySQL database password */
define('DB_PASSWORD', ')5S4@Y6gP8');

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
define('AUTH_KEY',         'imdquprrt9qbdb754d9svkjnj9qeir0zrbxhyevgh3uhpooictugebhg6w0j7lrb');
define('SECURE_AUTH_KEY',  'sxkd5kx8ruazvs3ovvw7iaqtqz0pir2z80csoms2jakw905dme6inkoq3vxnv3nn');
define('LOGGED_IN_KEY',    'cibl7upiklqoewmlv32mdqh7mkvlajippqvlf6ymerowz4slef8965oxrusumsoz');
define('NONCE_KEY',        'dpoopr2cngbyd41jfd0dtbok0h7vyeebxnhv9exjfhltrkemji1ymdchqgrzcoeg');
define('AUTH_SALT',        'xvqeema3ref5sk8xorbshh2y8n8c0ofm6ie6hkywuyshmeilgi6hui7u0djleo87');
define('SECURE_AUTH_SALT', 'bxknbzcvzgnpfbtmzylyduaejd7z8m2jedtqwtwhejr9fckxu3pevdzvbqrtz9oh');
define('LOGGED_IN_SALT',   'ywtmrhcpmz10jsavuwtqyyy9vdd0bf2bjgasfe9rd1oxszkm0y62aevjwgmoumpi');
define('NONCE_SALT',       'qfj4kaozsah0iqrkgnh4tmx6vsczidlge3s2ysjbrk1vs4rbwz9lxb08uj1aaub5');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'skm_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define( 'WP_MEMORY_LIMIT', '128M' );
define( 'WP_AUTO_UPDATE_CORE', false );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

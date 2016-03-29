<?php
define('DEFAULT_PROJECT', 'home'); // default app controller to use
define("COPYRIGHT", "Robert Strutts, Chris Allen");
define('CX_SITE_NAME', 'CX Site');
define("CX_PAGE_TITLE","The CX HomePage");

if (!defined("CX_KEYWORDS")) {
  define("CX_KEYWORDS","");
}
if (!defined("CX_DESCRIPTION")) {
  define("CX_DESCRIPTION","");
}

if (!defined("CX_ROBOTS")) {
  define('CX_ROBOTS', 'INDEX, FOLLOW, ARCHIVE');
}

cx_configure::set('cx', array(
  'live' => isset($_SERVER['live']) ? cx_bool($_SERVER['live']) : true,
  'short_url' => true, // Is Apache Mod_Rewrite turned ON??
  'web_alert_timeout' => 4, // # of seconds before warning of timeout to developers on WEB Page/JS Console
  'seconds_to_log_slow_timeout' => 2, // # of seconds before appending to Slow Speed LOG File
  'session_variable' => 'HOME_SYS_', // set session variable name for project
  'login' => 'login_', // Name for login session variable, will be added to the prefix
  'logger_time_zone' => 'America/Detroit', // My Log Files, use my timezone
  'send_emails' => true, // Enable emails
  'email_on_errors' => isset($_SERVER['email_on_errors']) ? cx_bool($_SERVER['email_on_errors']) : false,
  'admin_name' => 'Bob', // Email Admin Name
  'admin_email' => 'Me@Localhost', // Email Admins on error
));

cx_configure::set('security', array(
  'main_key' => $_SERVER['DT_C'], // Gets Key code from Server
  'main_salt' => $_SERVER['DT_S'], // Get SALT code from Server
  'csrf_security_level' => 'high', // Stop Attacks at what cost??
  'retries_allowed_before_throttling' => 3, // Reties attempts allowed for login, before it throttles it...
  'throttling_login_seconds' => 20, // Seconds to deny more login attempts
  'session_name' => 'example_xxz', // More secure then PHPSESSID
  'session_table' => 'sessions', // DB PHP Session Table name, false = USE FILES
  'session_security_level' => 'low', // Faster Reqests keep low
));

cx_configure::set('database', array(
  // PDO::ERRMODE_WARNING
  // PDO::ERRMODE_SILENT  
  // PDO::ERRMODE_EXCEPTION
  'PDO_ERROR' => PDO::ERRMODE_EXCEPTION, 
  'PDO_TIMEOUT' => 6, // Seconds to wait to connect before error
  'PDO_PERSISTENT' => true, // Connection type
//  'SOCKET' => '/var/run/mysqld/mysqld.sock',
  'TYPE' => 'mysql',
  'HOST' => 'localhost',
  'PORT' => '3306',
  'NAME' => 'example',
  'USER' => $_SERVER['DT_U'],
  'PASS' => $_SERVER['DT_P'],
));


/*
 * Do NOT changes the lines of code below this line HERE: !!
 * ----------------------------------------------------------------------------
 * =========================Hands OFF!!========================================
 */
define('DS', DIRECTORY_SEPARATOR);
define('PROJECT_BASE_DIR', dirname(__FILE__) . DS);

unset($_SERVER['DT_U'], $_SERVER['DT_P']);
unset($_SERVER['REDIRECT_DT_U'], $_SERVER['REDIRECT_DT_P']);
unset($_SERVER['DT_C'], $_SERVER['DT_S']);
unset($_SERVER['REDIRECT_DT_C'], $_SERVER['REDIRECT_DT_S']);

$tzg = ini_get('date.timezone');
if (empty($tzg)) {
  cx_configure::set('php_timezone', "UTC");
}
/*
The first setting, “session.cookie_lifetime”, sets the time period in seconds 
 * that the cookie should exist in the user’s browser. The second setting, 
 * “session.gc_maxlifetime”, sets the minimum number of seconds that the session
 * information should be stored on the server. Set at 69,120 seconds, a user 
 * should be able to browse your application for 48 hours before needing to 
 * re-authenticate their session.
*/

ini_set('session.cookie_lifetime', 69120);
ini_set('session.gc_maxlifetime', 69120);
ini_set('session.gc_probability', 1); // Should be one!!!
ini_set('session.gc_divisor', 1000); // 1000 is a good production value(.01% chance), 100 is for development (1% chance)

function project_folder_name() {
  $url = $_SERVER['REQUEST_URI']; //returns the current URL
  $paths = dirname(__FILE__);
  $paths = str_replace('\\', "/", $paths); // Fix for Windows
  $folder_name = ltrim(substr($paths, strrpos($paths, '/')), '/');
  $found_folder = (stripos($url, $folder_name) !== false);
  if ($found_folder === false) {
    $folder_name = '';
  }

  $parts_before_folder = explode('/'.$folder_name, $url);
  $dir = $parts_before_folder[0] . '/' . $folder_name;
  return $dir;  
}

function cx_bool($bool) {
  return (strtolower($bool) === 'false' || strtolower($bool) === 'no' || strtolower($bool) === 'disable' || $bool === false) ? false : true;
}

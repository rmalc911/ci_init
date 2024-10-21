<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  or define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

// Custom
define('ADMIN_PATH', 'admin/');
define('ADMIN_LOGIN_PATH', ADMIN_PATH . 'login/');
define('ADMIN_VIEWS_PATH', 'admin/');
define('ADMIN_ASSETS_PATH', 'assets/admin/');

define('ADMIN_LOGIN_REDIRECT', ADMIN_PATH . 'home/config_profile');

define('BANNER_UPLOAD_PATH', 'assets/uploads/banners/');
define('GALLERY_UPLOAD_PATH', 'assets/uploads/gallery/');
define('RESUME_UPLOAD_PATH', 'assets/uploads/resume/');
define('PROFILE_LOGO_UPLOAD_PATH', 'assets/uploads/logo/');

define('PROFILE_LOGO_FIELD', 'profile_logo');
define('PROFILE_FAVICON_FIELD', 'profile_favicon');

define('SOCIAL_MEDIA_NAMES', [
	'' => '',
	'facebook-f' => 'Facebook',
	'twitter' => 'Twitter',
	'x-twitter' => 'X (Twitter)',
	'instagram' => 'Instagram',
	'linkedin-in' => 'LinkedIn',
	'youtube' => 'Youtube',
	'whatsapp' => 'WhatsApp',
	'pinterest' => 'Pinterest',
	'vimeo' => 'Vimeo',
	'flickr' => 'Flickr',
	'dribbble' => 'Dribbble',
	'behance' => 'Behance',
	'github' => 'Github',
]);

define('LOGO26', 'assets/images/26_footer_dark.png');
define('LOGO26_LIGHT', 'assets/images/26_footer.png');
define('LOGO26_URL', 'https://steed26.com/');

define('date_format', 'Y-m-d');
define('time_format', 'H:i:s');
define('date_time_format', 'Y-m-d H:i:s');
define('user_date', 'jS M Y');
define('user_date_d', 'D, jS M Y');
define('user_time', 'g:i A');
define('user_date_time', 'jS M Y, g:i A');
define('input_date', 'd-m-Y');
define('input_time', 'g:i A');
define('input_date_time', 'd-m-Y g:i A');
define('db_user_date', '%D %b %Y');
define('db_user_time', '%l:%i&nbsp;%p');
define('db_user_date_time', '%D %b %Y -<wbr> %l:%i&nbsp;%p');
define('db_date', '%Y-%m-%d');
define('db_time', '%H:%i:%s');
define('db_date_time', '%Y-%m-%d %H:%i:%s');
define('db_input_date', '%d-%m-%Y');
define('db_input_time', '%l:%i&nbsp;%p');
define('db_input_date_time', '%d-%m-%Y %l:%i&nbsp;%p');

define('CATEGORY_VIEW_DEPTH', 3);
define('IMG_SPLIT', '~##~');

// UPLOAD_MAX_SIZE = true; // use server limit
define('UPLOAD_MAX_SIZE', '8M');

final class ShareUrl {
	public static function facebook($url) {
		return 'https://facebook.com/sharer/sharer.php?u=' . $url;
	}
	public static function twitter($url) {
		return 'https://x.com/intent/post?url=' . $url;
	}
	public static function linkedin($url) {
		return 'https://linkedin.com/shareArticle?mini=true&url=' . $url;
	}
	public static function whatsapp($url) {
		return 'https://api.whatsapp.com/send?text=' . $url;
	}
	public static function pinterest($url) {
		return 'https://pinterest.com/pin/create/button/?url=' . $url;
	}
}

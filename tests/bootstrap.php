<?php

declare(strict_types=1);

$root = dirname(__DIR__);

define('DIR_OPENCART', $root . '/upload/');
define('DIR_SYSTEM', DIR_OPENCART . 'system/');
define('DIR_APPLICATION', DIR_OPENCART . 'catalog/');
define('DIR_EXTENSION', DIR_OPENCART . 'extension/');
define('DIR_IMAGE', DIR_OPENCART . 'image/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', sys_get_temp_dir() . '/opencart_test_cache_' . getmypid() . '/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', sys_get_temp_dir() . '/opencart_test_logs_' . getmypid() . '/');
define('DIR_SESSION', sys_get_temp_dir() . '/opencart_test_session_' . getmypid() . '/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');
define('DB_PREFIX', getenv('OC_DB_PREFIX') ?: 'oc_');
define('CACHE_PREFIX', 'test_');

if (!is_dir(DIR_CACHE)) {
	mkdir(DIR_CACHE, 0777, true);
}
if (!is_dir(DIR_LOGS)) {
	mkdir(DIR_LOGS, 0777, true);
}
if (!is_dir(DIR_SESSION)) {
	mkdir(DIR_SESSION, 0777, true);
}

require_once DIR_SYSTEM . 'storage/vendor/autoload.php';
require_once DIR_SYSTEM . 'engine/autoloader.php';
require_once DIR_SYSTEM . 'engine/config.php';
require_once DIR_SYSTEM . 'engine/registry.php';
require_once DIR_SYSTEM . 'engine/controller.php';
require_once DIR_SYSTEM . 'engine/model.php';
require_once DIR_SYSTEM . 'engine/factory.php';
require_once DIR_SYSTEM . 'engine/action.php';
require_once DIR_SYSTEM . 'engine/event.php';
require_once DIR_SYSTEM . 'engine/proxy.php';
require_once DIR_SYSTEM . 'engine/loader.php';

require_once DIR_SYSTEM . 'helper/general.php';
require_once DIR_SYSTEM . 'helper/bbcode.php';
require_once DIR_SYSTEM . 'helper/yaml.php';
require_once DIR_SYSTEM . 'helper/db_schema.php';

$autoloader = new \Opencart\System\Engine\Autoloader();
$autoloader->register('Opencart\System', DIR_SYSTEM);
$autoloader->register('Opencart\Test', dirname(__DIR__) . '/tests/fixtures/');

spl_autoload_register(static function (string $class) use ($autoloader): void {
	if (str_starts_with($class, 'Tests\\')) {
		$relative = str_replace('\\', '/', substr($class, strlen('Tests\\')));
		$file = __DIR__ . '/' . $relative . '.php';
		if (is_file($file)) {
			require_once $file;

			return;
		}
	}

	$autoloader->load($class);
});

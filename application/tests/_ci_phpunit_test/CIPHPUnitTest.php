<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTest
{
	private static $loader_class = 'CI_Loader';
	private static $config_class = 'CI_Config';
	private static $controller_class;
	private static $autoload_dirs;

	/**
	 * Initialize CIPHPUnitTest
	 *
	 * @param array $autoload_dirs directories to search class file for autoloader
	 *
	 * Exclude from code coverage: This is test suite bootstrap code, so we
	 * know it's executed, but because it's bootstrap code, it runs outside of
	 * any coverage tracking.
	 *
	 * @codeCoverageIgnore
	 */
	public static function init(array $autoload_dirs = null)
	{
		self::defineConstants();

		// Fix CLI args
		$_server_backup = $_SERVER;
		$_SERVER['argv'] = [
			'index.php',
			'welcome'	// Dummy
		];
		$_SERVER['argc'] = 2;

		self::$autoload_dirs = $autoload_dirs;

		$cwd_backup = getcwd();

		// Load autoloader for ci-phpunit-test
		require __DIR__ . '/autoloader.php';

		self::loadTestCaseClasses();

		// Replace a few Common functions
		require __DIR__ . '/replacing/core/Common.php';
		require BASEPATH . 'core/Common.php';

		// Workaround for missing CodeIgniter's error handler
		// See https://github.com/kenjis/ci-phpunit-test/issues/37
		set_error_handler('_error_handler');

		// Load new functions of CIPHPUnitTest
		require __DIR__ . '/functions.php';
		// Load ci-phpunit-test CI_Loader
		require __DIR__ . '/replacing/core/Loader.php';
		// Load ci-phpunit-test CI_Input
		require __DIR__ . '/replacing/core/Input.php';
		// Load ci-phpunit-test CI_Output
		require __DIR__ . '/replacing/core/Output.php';

		// Change current directory
		chdir(FCPATH);

		self::loadCodeIgniter();

		// Create CodeIgniter instance
		if (! self::wiredesignzHmvcInstalled())
		{
			new CI_Controller();
		}
		else
		{
			new MX_Controller();
		}

		// This code is here, not to cause errors with HMVC
		self::replaceLoader();
		if (self::wiredesignzHmvcInstalled()) {
			self::replaceConfig();
		}

		// Restore $_SERVER. We need this for NetBeans
		$_SERVER = $_server_backup;

		// Restore cwd to use `Usage: phpunit [options] <directory>`
		chdir($cwd_backup);
	}

	private static function loadCodeIgniter(){
		// Load constants.php before replacing helpers,
		// because config_item() loads config.php
		if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/constants.php'))
		{
			require_once(APPPATH.'config/'.ENVIRONMENT.'/constants.php');
		}

		if (file_exists(APPPATH.'config/constants.php'))
		{
			require_once(APPPATH.'config/constants.php');
		}

		// Replace helpers before loading CI (which could auto load helpers)
		self::replaceHelpers();

		/*
		 * --------------------------------------------------------------------
		 * LOAD THE BOOTSTRAP FILE
		 * --------------------------------------------------------------------
		 *
		 * And away we go...
		 */
		require __DIR__ . '/replacing/core/CodeIgniter.php';
	}

	private static function defineConstants()
	{
		if (! defined('TESTPATH')) {
			define('TESTPATH', APPPATH.'tests'.DIRECTORY_SEPARATOR);
		}
		// Current Bootstrap.php should define this, but in case it doesn't:
		if (! defined('CI_PHPUNIT_TESTPATH')) {
			define('CI_PHPUNIT_TESTPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
		}
	}

	private static function loadTestCaseClasses()
	{
		require TESTPATH . 'TestCase.php';

		$db_test_case_file = TESTPATH . 'DbTestCase.php';
		if (is_readable($db_test_case_file))
		{
			require $db_test_case_file;
		}

		$unit_test_case_file = TESTPATH . 'UnitTestCase.php';
		if (is_readable($unit_test_case_file))
		{
			require $unit_test_case_file;
		}
	}

	/**
	 * @param bool $use_my_controller
	 */
	public static function createCodeIgniterInstance($use_my_controller = false)
	{
		if (! self::wiredesignzHmvcInstalled())
		{
			if ($use_my_controller && self::hasMyController())
			{
				new self::$controller_class;
			}
			else
			{
				new CI_Controller();
			}
		}
		else
		{
			new CI();
			new MX_Controller();
		}
	}

	private static function hasMyController()
	{
		if (self::$controller_class !== null) {
			return self::$controller_class !== 'CI_Controller';
		}

		$my_controller_file =
			APPPATH . 'core/' . config_item('subclass_prefix') . 'Controller.php';

		if (file_exists($my_controller_file))
		{
			$controller_class = config_item('subclass_prefix') . 'Controller';
			if ( ! class_exists($controller_class))
			{
				require $my_controller_file;
			}

			self::$controller_class = $controller_class;
			return true;
		}

		self::$controller_class = 'CI_Controller';
		return false;
	}

	public static function wiredesignzHmvcInstalled()
	{
		if (file_exists(APPPATH.'third_party/MX'))
		{
			return true;
		}

		return false;
	}

	public static function getAutoloadDirs()
	{
		return self::$autoload_dirs;
	}

	protected static function replaceLoader()
	{
		$my_loader_file =
			APPPATH . 'core/' . config_item('subclass_prefix') . 'Loader.php';

		if (file_exists($my_loader_file))
		{
			self::$loader_class = config_item('subclass_prefix') . 'Loader';
			if ( ! class_exists(self::$loader_class))
			{
				require $my_loader_file;
			}
		}
		self::loadLoader();
	}

	protected static function replaceConfig()
	{
		$my_config_file =
			APPPATH . 'core/' . config_item('subclass_prefix') . 'Config.php';

		if (file_exists($my_config_file))
		{
			self::$config_class = config_item('subclass_prefix') . 'Config';
			if ( ! class_exists(self::$config_class))
			{
				require $my_config_file;
			}
		}
		self::loadConfig();
	}

	protected static function replaceHelpers()
	{
		$helpers = ['url_helper', 'download_helper'];
		foreach ($helpers as $helper) {
			static::loadHelper($helper);
		}
	}

	protected static function loadHelper($helper)
	{
		$my_helper_file = APPPATH . 'helpers/' . config_item('subclass_prefix') . $helper . '.php';
		if (file_exists($my_helper_file))
		{
			require $my_helper_file;
		}
		require __DIR__ . '/replacing/helpers/' . $helper . '.php';
	}

	public static function setPatcherCacheDir($dir = null)
	{
		if ($dir === null)
		{
			$dir = CI_PHPUNIT_TESTPATH . 'tmp/cache';
		}

		MonkeyPatchManager::setCacheDir(
			$dir
		);
	}

	public static function loadLoader()
	{
		$loader = new self::$loader_class;
		load_class_instance('Loader', $loader);
	}

	public static function loadConfig()
	{
		$config= new self::$config_class;
		load_class_instance('Config', $config);
	}
}

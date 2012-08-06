<?php

namespace VojtechDobes;

use Nette;
use Nette\Config;
use Nette\Utils\Arrays;
use Nette\Utils\Finder;
use Nette\Utils\Json;


/**
 * Autoregisters extensions downloaded via Composer
 *
 * @author  Vojtěch Dobeš
 * @license New BSD
 */
class ComposerExtension extends Config\CompilerExtension
{

	/** @var array [package name => alias] */
	private $alias = array(
		'kdyby/curl-extension' => 'curl',
		// ...
	);



	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig(array(
			'default' => TRUE,
		));

		if ($config['default'] === TRUE) {
			$config['alias'] = array_merge($this->alias, $config['alias']);
		}

		$vendorDir = __DIR__ . '/../..';

		$composerFile = isset($config['file']) ? $config['file'] : $vendorDir . '/../composer.lock';
		$packages = Arrays::get(Json::decode(file_get_contents($composerFile)), 'packages');
		foreach ($packages as $package) {
			$name = $package['package'];
			if (isset($config['alias'][$name])) {
				$name = $config['alias'][$name];
			} elseif ($config['default'] == 'short') {
				$name = explode('/', $name);
				$name = $name[1];
			}

			foreach (Finder::findFiles('*.php')->from($vendorDir . '/' . $package['package']) as $file) {
				$content = file_get_contents($file->getPathName());
				if (stripos($content, 'namespace') !== FALSE) {
					$namespace = preg_replace('/namespace\ +(\w+);/i', '$1', $content);
				}
				$tokens = token_get_all($content);
				$count = count($tokens);
				for ($i = 2; $i < $count; $i++) {
					if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING && !($tokens[$i - 3] && $tokens[$i - 4][0] == T_ABSTRACT)) {
						$class = (isset($namespace) ? $namespace . '\\' : '') . $tokens[$i][1];
						if ($class == 'VojtechDobes\ComposerExtension') continue;
						if (ClassType::from($class)->isSubclassOf('Nette\Config\CompilerExtension')) {
							$this->compiler->addExtension($name, new $class);
							break 2;
						}
					}
				}
			}
		}
	}

}

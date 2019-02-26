<?php declare(strict_types=1);
namespace laudirbispo\EasyRouter;
/**
 * Copyright (c) Laudir Bispo  (laudirbispo@outlook.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     (c) Laudir Bispo  (laudirbispo@outlook.com)
 * @version       1.0.1
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @package       laudirbispo\EasyRouter
 */
use laudirbispo\classname\ClassName;

class EasyRouter 
{
	/**
	 * Routes pattern
	 *
	 * @var array
	 */
	protected $routes = [];
	
	protected $baseDir;
	
	public function __construct ($baseDir = null) 
	{
		if (null === $baseDir)
			$baseDir = $_SERVER['DOCUMENT_ROOT'];
		
		$this->baseDir = $baseDir;
	}
	
	/**
	 * Add to Routes
	 *
	 * @param $patterns (mixed) string|array
	 * @param $callback (mixed) string|closure
	 * @return void
	 */
	public function add($patterns, $callback) : void
	{
		if (is_array($patterns)) {
			foreach ($patterns as $pattern) {
				$this->addRoute($pattern, $callback);
			}
		} else if (is_string($patterns)) {
			$this->addRoute($patterns, $callback);
		}
		return;
	}
	
	/**
	 * Add Route
	 */
	private function addRoute($pattern, $callback)
	{
		if (!is_string($pattern)) 
			throw new Exceptions\InvalidRoute('Invalid route: ' . $pattern);
		
		$pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
		if (!isset($this->routes[$pattern]))
			$this->routes[$pattern] = $callback;
	}
	
	/**
	 * Check if exists Route pattern
	 *
	 * @param $route (string) 
	 */
	public function hasRoute(string $pattern) : bool
	{
		$pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
		return isset($this->routes[$pattern]);
	}
	
	/**
	 * Execute the Route
	 *
	 * @param $url (string) - 
	 * 
	 */
	public function execute(string $url = '/') 
    {
		foreach ($this->routes as $pattern => $callback) 
        {	
			if (preg_match($pattern, $url, $params)) {
                if (is_string($callback) && strpos($callback, '::'))  {
                    list($controller, $method) = explode('::', $callback);
					// Check if controllers exists
					$classname = ClassName::path($controller);
					$filename = $this->baseDir .'/'. $classname . '.php';
					if (!is_readable($filename)) {
                        throw new Exceptions\ControllerDoesNotExists(
							sprintf("Controller [%s] does not exist or could not be found.", $controller)
						);
                    }
					if (!method_exists($controller, $method)) {
						throw new Exceptions\MethodDoesNotExists(
							sprintf("Method [%s], does not exist in controller [%s].", $method, $controller)
						);
                    }
                    $callback = array(new $controller, $method);
                }
				array_shift($params);
				return call_user_func_array($callback, array_values($params));
			}
		}
		
		throw new Exceptions\RouteNotFound('No routes registered to the current address.');
	}

}

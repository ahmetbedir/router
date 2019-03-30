<?php

namespace BDR;
/**
 * Router by ahmet bedir <info@ahmetbedir.net>
 */
class Route {
	public static $routes 	 	= [];
	public static $methods 	 	= [];
	public static $callbacks 	= [];
	public static $controller	= [];
	public static $routeFounded = false;

	public static function __callstatic($method, $params) {
		$allowedMethos = [
			'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'ANY', 'AJAX',
		];
		if (in_array(strtoupper($method), $allowedMethos)) {
			array_push(self::$routes, $params[0]);
			array_push(self::$methods, strtoupper($method));
			array_push(self::$callbacks, $params[1]);
		}
	}

	public static function dispatch() {
		$requestURI = trim($_GET['route'], '/').'/';

		foreach (self::$routes as $routeKey => $route) {
			$patternRegex = preg_replace("/\{(.*?)(\?)?\}/", "(?'$1'.+)$2", $route);
			$patternRegex = "#^" . trim($patternRegex, '/') . "$#";
			
			preg_match($patternRegex, $requestURI, $routeMatches);

			// Eşleşen bir rota yok ise diğer rotaya geç
			if (!$routeMatches) continue;

			if(!self::$routeFounded) self::$routeFounded = true;

			$routingCallback = self::$callbacks[$routeKey];
			$routeParams = array_slice(array_unique($routeMatches), 1);

			if (is_callable($routingCallback)) {
				call_user_func_array($routingCallback, $routeParams);
			}
		}

		if(!self::$routeFounded) {
			die("Sayfa bulunamadı!");
		}
	}
}
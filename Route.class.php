<?php

namespace BDR;

/**
 * 
 */
class Route {
	private static $routes 	 			= array();
	private static $methods 	 		= array();
	private static $actions 			= array();

	private static $matchedRoute		= array();
	private static $matchedRouteParams 	= array();
	private static $routeFound 			= false;
	private static $fallback			= false;

	private static $currentRoute		= array();
	private static $currentRouteAction	= array();
	
	/**
	 * GET Rotasını tanımlar
	 *
	 * @param string $route
	 * @param string|closure $action
	 * @return void
	 */
	public static function get($route, $action){
		self::addRoute(['GET'], $route, $action);
	}

	/**
	 * POST Rotasını tanımlar
	 *
	 * @param string $route
	 * @param string|closure $action
	 * @return void
	 */
	public static function post($route, $action){
		self::addRoute(['POST'], $route, $action);
	}

	/**
	 * PUT Rotasını tanımlar
	 *
	 * @param string $route
	 * @param string|closure $action
	 * @return void
	 */
	public static function put($route, $action){
		self::addRoute(['PUT'], $route, $action);
	}

	/**
	 * DELETE Rotasını tanımlar
	 *
	 * @param string $route
	 * @param string|closure $action
	 * @return void
	 */
	public static function delete($route, $action){
		self::addRoute(['DELETE'], $route, $action);
	}

	/**
	 * Tüm metholarda çalışabilen rotayı tanımlar
	 *
	 * @param string $route
	 * @param string|closure $action
	 * @return void
	 */
	public static function any($route, $action){
		self::addRoute(['GET','POST', 'PUT', 'PATCH', 'DELETE',], $route, $action);
	}

	/**
	 * Gönderilen ilk parametredki methodlara uygun çalışan rotayı tanımlar
	 *
	 * @param string $route
	 * @param string|closure $action
	 * @return void
	 */	
	public static function match($methods = [], $route, $action){
		self::addRoute($methods, $route, $action);
	}

	/**
	 * Rotaları ekler
	 *
	 * @param string|array $method
	 * @param string $route
	 * @param string|closure $action
	 * @return void
	 */
	private static function addRoute($method, $route, $action){
		$method = array_map('strtoupper', $method);
		
		array_push(self::$methods, $method);
		array_push(self::$routes, $route);
		array_push(self::$actions, $action);
	}

	/**
	 * Eşleşen rotanın aksiyonu hazırlar
	 *
	 * @param integer $routeIndex
	 * @return void
	 */
	private static function startRoute($routeIndex){
		self::$routeFound = !self::$routeFound ?? true;

		self::$matchedRouteParams = array_slice(array_unique(self::$matchedRoute), 1);
		
		self::$currentRoute = self::$matchedRoute[0];
		
		self::$currentRouteAction = self::$actions[$routeIndex];
		
		self::runRoute();
	}

	/**
	 * Rota aksiyonu tanımlar Closure yada Class
	 * Bulunana rotaya göre çalıştırma işlemini başlatır.
	 *
	 * @return void
	 */
	private static function runRoute(){
		if(is_string(self::$currentRouteAction)) 
			return self::classMethod();

		if(is_object(self::$currentRouteAction) && (self::$currentRouteAction instanceof \Closure))
			return self::closureMethod();
	}

	/**
	 * Rota aksiyonunda ki stringten sınıf ve method ayrırır
	 * Sınıfı yaratır ve rota parametrelerini göndererek methodu çalıştırı.
	 *
	 * @return void
	 */
	private static function classMethod(){
		$class = explode('@', self::$currentRouteAction);

		call_user_func(array(new $class[0], $class[1]), self::$matchedRouteParams);
	}

	/**
	 * Anonim fonksiyonu çalıştırır ve parametleri gönderir
	 *
	 * @return void
	 */
	private static function closureMethod(){
		call_user_func_array(self::$currentRouteAction, self::$matchedRouteParams);
	}

	/**
	 * Rota eşleşmemesi durumunda özel bir 
	 * fonksiyon tanımlamak tanımlamak için kullanılır
	 *
	 * @return void
	 */
	public static function fallback($closure = false){
		self::$fallback = $closure;
	}

	/**
	 * Rota kontrolleri sonrası eşleşen bir rota yok ise çalşır
	 *
	 * @return void
	 */
	private static function routeNotFound(){
		if(self::$routeFound) return false;

		if(is_callable(self::$fallback)){
			call_user_func(self::$fallback);
		}else{
			throw new \Exception('Sayfa bulunamadı!');
		}

	}

	/**
	 * Rota methodu ile itek methodunu karşılaştırır.
	 *
	 * @param integer $routeIndex
	 * @return boolean
	 */
	private static function checkMethod($routeIndex){
		$normalyMethod 	= in_array($_SERVER['REQUEST_METHOD'], self::$methods[$routeIndex]);
		
		$hiddenMethod	= (isset($_REQUEST['_method']) && in_array($_REQUEST['_method'], self::$methods[$routeIndex]));

		return ($normalyMethod || $hiddenMethod) ? true : false;
	}

	/**
	 * Rota ile istek url arasındaki eşleşmeyi kontrol eder
	 *
	 * @param string $route
	 * @return array|boolean
	 */
	private static function routeMatch($route){
		$requestURI = trim($_GET['url'], '/');
		$routePattern = preg_replace("/\{(.*?)\}/", "(?'$1'[\w-]+)", $route);
		
		$routePattern = "#^" . trim($routePattern, '/') . "$#";
		
		preg_match($routePattern, $requestURI, $matchedRoute);

		return $matchedRoute ?? false;
	}

	/**
	 * Tüm rotalrın tanımlaması bittikten sonra rota kontrolünü gerçekleştirir.
	 *
	 * @return void
	 */
	public static function dispatch() {

		foreach (self::$routes as $routeIndex => $route) {
			self::$matchedRoute = self::routeMatch($route);

			// Eşleşen bir rota yok ise diğer rotaya geç
			if (!self::$matchedRoute) continue;

			// İstek methodu ile rota methodunu kontrol et
			if(self::checkMethod($routeIndex))
				self::startRoute($routeIndex);
		}

		
		self::routeNotFound();
	}
}
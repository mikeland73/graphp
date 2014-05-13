<?

class GPRouter extends GPObject {

  private static
    $routes,
    $parts;

  public static function init() {
    self::$routes = require_once ROOT_PATH.'config/routes.php';
    self::process();
    // TODO (mikeland86): allow override of default routing
    self::defaultRouting();
  }

  private static function process() {
    $uri = $_SERVER['REQUEST_URI'];
    $uri = str_replace('index.php', '', $uri);
    $uri = preg_replace('/\?.*/', '', $uri);
    if (isset(self::$routes[$uri])) {
      // TODO (mikeland86): Add regex support
      self::$parts = self::$routes[$uri];
    } else {
      self::$parts = array_values(array_filter(explode('/', $uri)));
    }
  }

  private static function defaultRouting() {
    // error handling. check controller exists. check method exists.
    $controller_name = ucfirst(idxx(self::$parts, 0));
    $method_name = idx(self::$parts, 1, 'index');
    GPLoader::sharedInstance()->loadController($controller_name);
    $controller = new $controller_name();
    $args = array_slice(self::$parts, 2);
    call_user_func_array([$controller, $method_name], $args);
  }
}

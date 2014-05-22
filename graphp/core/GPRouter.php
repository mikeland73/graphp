<?

class GPRouter extends GPObject {

  private static
    $routes,
    $parts;

  public static function init() {
    self::$routes = require_once ROOT_PATH.'config/routes.php';
    self::$parts = self::getParts();
    self::route();
  }

  private static function getParts() {
    $uri = $_SERVER['REQUEST_URI'];
    $uri = str_replace('index.php', '', $uri);
    $uri = preg_replace(['/\?.*/', '#[/]+$#'], '', $uri);
    if (!$uri && isset(self::$routes['__default__'])) {
      return self::$routes[$uri];
    }
    foreach (array_keys(self::$routes) as $regex) {
      $matches = [];
      if (preg_match('#'.$regex.'#', $uri, $matches)) {
        $parts = self::$routes[$regex];
        array_concat_in_place($parts, array_slice($matches, 1));
        return $parts;
      }
    }
    return array_values(array_filter(explode('/', $uri)));
  }

  private static function route() {
    $controller_name = ucfirst(idxx(self::$parts, 0));
    $method_name = idx(self::$parts, 1, 'index');
    GPLoader::sharedInstance()->loadController($controller_name);
    $controller = new $controller_name();
    $args = array_slice(self::$parts, 2);
    call_user_func_array([$controller, $method_name], $args);
  }
}

<?

class GPRouter extends GPObject {

  private static
    $routes,
    $parts;

  public static function init() {
    self::$routes = require_once ROOT_PATH.'config/routes.php';
    self::process();
    self::route();
  }

  private static function process() {
    $uri = $_SERVER['REQUEST_URI'];
    $uri = str_replace('index.php', '', $uri);
    $uri = preg_replace(['/\?.*/', '#[/]+$#'], '', $uri);

    foreach (array_keys(self::$routes) as $regex) {
      $matches = [];
      if ($regex && preg_match('#'.$regex.'#', $uri, $matches)) {
        self::$parts = self::$routes[$regex];
        array_concat_in_place(self::$parts, array_slice($matches, 1));
        return;
      }
    }
    self::$parts = array_values(array_filter(explode('/', $uri)));
  }

  private static function route() {
    // error handling. check controller exists. check method exists.
    $controller_name = ucfirst(idxx(self::$parts, 0));
    $method_name = idx(self::$parts, 1, 'index');
    GPLoader::sharedInstance()->loadController($controller_name);
    $controller = new $controller_name();
    $args = array_slice(self::$parts, 2);
    call_user_func_array([$controller, $method_name], $args);
  }
}

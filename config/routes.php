<?

return [
  // default route.
  '' => ['welcome', 'index'],

  // custom routes. Regex allowed, "#" not allowed. Don't end with slash if
  // you want non slash to match. Use capture groups for arguments:
  // '/id/(\d+)' => ['Controller', 'index'], passes the capture group match
  // into Controller::index method call.
  '/user/(\d+)' => ['welcome', 'index'],
];

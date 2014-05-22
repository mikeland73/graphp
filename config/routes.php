<?

return [
  // default route. You can rmeove if you don't want one.
  '__default__' => ['welcome', 'index'],

  // Custom regex routes, "#" not allowed. Don't end with slash if
  // you want non slash to match (or use /?). Use capture groups for arguments:
  // '^/id/(\d+)/?$' => ['Controller', 'index'], passes the capture group match
  // into Controller::index method call.
  '^/user/([ab]+)/?$' => ['welcome', 'index'],
];

<?php

return [
  'is_dev'        => true,

  'domain'        => 'localhost',
  'use_index_php' => true, // To avoid this, you need to configure your server
  'handler_suffix' => 'ControllerHandler',

  // Security
  'salt'          => 'CHANGE THIS TO ANY RANDOM STRING',
  'admin_enabled' => true,

  'cookie_exp'    => '1209600', // Two weeks in seconds
  'cookie_domain' => '',
  'cookie_name'   => 'session',

  'view_404'      => '',
  'layout_404'    => '',

  'app_folder'    => 'app',

  // This will automatically drop the DB before the first view is rendered
  'disallow_view_db_access' => false,
];

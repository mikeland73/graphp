<?

return [
  'is_dev'        => true,
  
  'domain'        => 'localhost',
  'use_index_php' => true, // To avoid this, you need to configure your server

  // Security
  'salt'          => 'CHANGE THIS TO ANY RANDOM STRING',
  'admin_enabled' => true,

  'cookie_exp'    => '1209600', // Two weeks in seconds
  'cookie_domain' => '',
  'cookie_name'   => 'session',
];

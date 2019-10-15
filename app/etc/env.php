<?php
return array (
  'backend' => 
  array (
    'frontName' => 'admin',
  ),
  'db' => 
  array (
    'connection' => 
    array (
      'indexer' => 
      array (
        'host' => 'localhost',
        'dbname' => 'local_stonestrand',
        'username' => 'root',
        'password' => '',
        'active' => '1',
        'persistent' => NULL,
      ),
      'default' => 
      array (
        'host' => 'localhost',
        'dbname' => 'local_stonestrand',
        'username' => 'root',
        'password' => '',
        'active' => '1',
      ),
      'checkout' => 
      array (
        'host' => 'localhost',
        'dbname' => 'local_stonestrand_checkout',
        'username' => 'root',
        'password' => '',
        'model' => 'mysql4',
        'engine' => 'innodb',
        'initStatements' => 'SET NAMES utf8;',
        'active' => '1',
      ),
      'sales' => 
      array (
        'host' => 'localhost',
        'dbname' => 'local_stonestrand_oms',
        'username' => 'root',
        'password' => '',
        'model' => 'mysql4',
        'engine' => 'innodb',
        'initStatements' => 'SET NAMES utf8;',
        'active' => '1',
      ),
    ),
    'table_prefix' => '',
  ),
  'install' => 
  array (
    'date' => 'Tue, 05 Apr 2016 07:38:21 +0000',
  ),
  'crypt' => 
  array (
    'key' => '4e371ecf55328aef3bf0e2ebebf5e74c',
  ),
  'session' => 
  array (
    'save' => 'files',
  ),
  'resource' => 
  array (
    'default_setup' => 
    array (
      'connection' => 'default',
    ),
    'checkout' => 
    array (
      'connection' => 'checkout',
    ),
    'sales' => 
    array (
      'connection' => 'sales',
    ),
  ),
  'x-frame-options' => 'SAMEORIGIN',
  'MAGE_MODE' => 'production',
  'cache_types' => 
  array (
    'config' => 1,
    'layout' => 1,
    'block_html' => 1,
    'collections' => 1,
    'reflection' => 1,
    'db_ddl' => 1,
    'eav' => 1,
    'full_page' => 1,
    'config_integration' => 1,
    'config_integration_api' => 1,
    'target_rule' => 1,
    'translate' => 1,
    'config_webservice' => 1,
  ),
);

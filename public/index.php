<?php
   define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
   define('App', ROOT . 'app' . DIRECTORY_SEPARATOR);
   define('Controllers', ROOT . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR);
   define('Core', ROOT . 'app' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR);
   define('Model', ROOT . 'app' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR);
   define('Views', ROOT . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);
   define('Database', ROOT . 'app' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR);

   $modules= [ROOT, App, Controllers, Core, Database];

   set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR , $modules));
   spl_autoload_register('spl_autoload', false);

   new Application;
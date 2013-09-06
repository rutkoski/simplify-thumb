<?php

if (! class_exists('Simplify_Autoload')) {
  require_once ('Simplify' . DIRECTORY_SEPARATOR . 'Autoload.php');

  sy_autoload_register(array('Simplify_Autoload', 'autoload'));
}

Simplify_Autoload::registerPath(dirname(__file__));

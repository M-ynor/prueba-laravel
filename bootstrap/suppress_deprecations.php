<?php

/**
 * Suprimir warnings de deprecación de PDO en PHP 8.5
 * 
 * PHP 8.5 depreca PDO::MYSQL_ATTR_SSL_CA en favor de Pdo\Mysql::ATTR_SSL_CA
 * Laravel 11.48 aún usa la constante antigua en vendor/laravel/framework/config/database.php
 */

error_reporting(E_ALL & ~E_DEPRECATED);

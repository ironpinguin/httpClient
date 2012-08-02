<?php
/**
 * Http browser bundle to make http[s] calls from a symfony project.
 *
 * Copyright see LICENCES file.
 *
 * User: michele
 * Date: 01.08.12 05:30
 */
if (file_exists($file = __DIR__.'/autoload.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once $file;
} elseif (file_exists($file = __DIR__.'/autoload.php.dist')) {
    /** @noinspection PhpIncludeInspection */
    require_once $file;
}
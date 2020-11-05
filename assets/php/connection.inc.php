<?php
/**
 * Create new MySQL class
 * to work with database
 */
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'sejarahku';

//var used to work with database
$condb = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if(mysqli_connect_errno()) {
    die('Could not connect to database: '.mysqli_connect_errno());
}
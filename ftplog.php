<?php

function getFtpConnection()
{
    static $connection = null;
    if ($connection !== null) return $connection;
    $host = '45.11.98.45';
    $username = 'emre';
    $password = 'emretest';

    // set up connection
    if (!$connection = ftp_connect($host)) {
        echo "couldn't connect to " . $host;
        return false;
    }
    // login with username and password
    if (!$login_result = ftp_login($connection, $username, $password)) {
        echo "couldn't connect as " . $username;
        return false;
    }

    return $connection;
}
function getCsvFiles()
{
if (!$connection = getFtpConnection()) return false;

$folder = '/';
// get list of files on given path
$files = ftp_nlist($connection, $folder);
if (!count($files)) {
    echo "folder is empty";
    return false;
}
$csvFiles = array();
foreach ($files as $file) {
  //Ftp Kontrol edilecek dosya uzantısını burda değiştir
    if (!preg_match('~\w+.cdr.badlog$~ism',$file)) continue;    
    $csvFiles[] = $folder . '/' . $file;
}
return $csvFiles;
}
function findNewestFile($files)
{
$mostRecent = array(
'time' => 0,
'file' => null
);
foreach ($files as $file) {
// get the last modified time for the file
$time = ftp_mdtm(getFtpConnection(), $file);
if ($time > $mostRecent['time']) {
    // this file is the most recent so far
    $mostRecent['time'] = $time;
    $mostRecent['file'] = $file;
}
}
return $mostRecent['file'];
}

// configuration
$dbtype = "mysql";
$dbhost = "localhost";
$dbname = "test";
$dbuser = "toor";
$dbpass = "toor";

// database connection
$conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);

// All FTP files
$files = getCsvFiles();
// find newest file
$newestFile = findNewestFile($files);

// query
$sql = "INSERT INTO csv_files (newestFile) VALUES (:newestFile)";
$q = $conn->prepare($sql);
$q->execute(array(':newestFile' => $newestFile));





?>

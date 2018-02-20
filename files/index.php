<?php

$file = 'test.txt';
$htAccessFile = '.htaccess';
$htPasswordFile = '.htpasswd';

$content =
    'AuthType Basic' . PHP_EOL .
    'AuthName "Restricted Access"' . PHP_EOL .
    'AuthUserFile /Applications/MAMP/htdocs/practice/files/.htpasswd' . PHP_EOL .
    'Require user tega';

//file_put_contents($htAccessFile, $content);
//file_put_contents($htPasswordFile, 'password');
//file_put_contents($file, 'Yo this is a new text', FILE_APPEND);

$filename = "something.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));


echo '<pre>';
$textToEncrypt = "he who doesn't do anything, doesn't go wrong -- Zeev Suraski";
$textToEncrypt = $contents;
$secretKey = "glop";
$method = 'AES-256-CBC-HMAC-SHA256';
$encrypted = openssl_encrypt($textToEncrypt, $method, $secretKey);
$decrypted = openssl_decrypt($encrypted, $method, $secretKey);
echo $encrypted . '<br>';
print_r($decrypted);
echo '</pre>';

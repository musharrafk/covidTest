<?php
$myFile = "/home/pionezjr/public_html/pioneeresolutions.in/hrms/index.php";
$fname = "demo.txt";
$fhandle = fopen($fname,"r");
$content = fread($fhandle,filesize($fname));

$content = str_replace("oldword", "newword", $content);

$fhandle = fopen($fname,"w");
fwrite($fhandle,$content);
fclose($fhandle);

?>
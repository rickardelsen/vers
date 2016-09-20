<?php
	$dir = "upload/extract/";
    chdir($dir);
    // exec("git log > ../log.txt");
    // $lines = file('../log.txt');
    $lones[] = exec("git log");
    // var_dump($lines);
    echo "\n\n\n";
    var_dump($lones);
?>
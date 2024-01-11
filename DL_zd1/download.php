<?php

include("php/session.php");

function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

$data = $_SESSION['exportTable'];

$fileName = $_SESSION['filename'] . date('Ymd') . ".xlsx";  

header("Content-Disposition: attachment; filename=\"$fileName\""); 
header("Content-Type: application/vnd.ms-excel"); 
                    
$flag = false; 
foreach($data as $row) { 
if(!$flag) { 
        // display column names as first row 
        echo implode("\t", array_keys($row)) . "\n"; 
        $flag = true; 
    } 
    // filter data 
    array_walk($row, 'filterData'); 
    echo implode("\t", array_values($row)) . "\n"; 
}

exit;
?>
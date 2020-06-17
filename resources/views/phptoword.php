<?php
$dir = "D:\laragon\www\whoyummy\app\Http";  // 需要转换的文件路径。
$fdir = "D:\laragon\www\whoyummy\app";  // 最终要放到的位置。
$arr = array($dir,$fdir);
ergodic($dir,$arr);
$text="";
function ergodic($dirname,$arr)
{
    $text="";
    if (is_dir($dirname)) {
        $handle = opendir($dirname);
        $a = $arr[1] . substr($dirname, strlen($arr[0]));
        while ($filename = readdir($handle)) {  // 读取一个打开的文件名。
            if ($filename != '.' && $filename != '..') {
                $dir = $dirname . '\\' . $filename; // 拼接目录名
                if (is_dir($dir)) {
                    ergodic($dir, $arr);  // 递归
                } else {

                    $c= file_get_contents($a . '\\' . $filename);
                    removeComment($c);
                    $text = $text.$c;
                }
            }
        }
    }
    echo $text;
}

function removeComment($content){
    return preg_replace("/(\/\*.*\*\/)|(#.*?\n)|(\/\/.*?\n)/s", '', str_replace(array("\r\n", "\r"), "\n", $content));
}

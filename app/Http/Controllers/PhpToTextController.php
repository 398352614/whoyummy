<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhpToTextController extends Controller
{
    public function phpToText(Request $request)
    {
        $txt="";
        $path = "$request->path";  // 需要转换的文件路径。
        $toPath= "$request->toPath";  // 最终要放到的位置。
        $txtName="$request->txtName";//文件名。
        $deleteComment="$request->deleteComment";//是否去注释。
        $findChinese=$request->findChinese;
        $stringArr = $this->get_filenamesbydir($path);
        foreach ($stringArr as $name => $content){
            $txt=$txt.PHP_EOL.$name.PHP_EOL.PHP_EOL.$content;
        }
        if($deleteComment==='1'){
            $txt=$this->removeComment($txt);
        }
        if($findChinese==='1'){
            $txt= $this->findChinese($txt);
        }
        $a=$toPath.'\\'.$txtName.'.txt';
        $file=fopen($a,"x");
        $row=file_put_contents($a,$txt);
        fclose($file);
        if(!empty($row)){
            return 'success';
        }
        return 'fail';
    }

    function get_allfiles($path, &$files, &$stringArr)
    {
        if (is_dir($path)) {
            $dp = dir($path);
            while ($file = $dp->read()) {
                if ($file !== "." && $file !== "..") {
                    $this->get_allfiles($path . "/" . $file, $files, $stringArr);
                }
            }
            $dp->close();
        }
        if (is_file($path)) {
            $files[] = $path;
            $stringArr[basename($path)] = file_get_contents($path);
        }
    }

    function get_filenamesbydir($dir)
    {
        $files = $stringArr = array();
        $this->get_allfiles($dir, $files, $stringArr);
        return $stringArr;
    }

    //删除注释
    function removeComment($content){
        return preg_replace("/(\/\*(\s|.)*?\*\/)|(\/\/.(\s|.*))|(#(\s*)?(.*))/", '', str_replace(array("\r\n", "\r"), "\n", $content));
    }

    //提取中文
    function findChinese($txt){
        preg_match_all("/[\x7f-\xff]+/", $txt, $x);
        return implode("\r",array_unique($x[0])) ;
    }

}



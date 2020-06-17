<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function route(Request $request){
        $data=[];
        $path = "$request->path";  // 需要转换的文件路径。
        $content =file_get_contents($path);
        preg_match_all ('/\'[a-zA-Z@]*\'/', $content,$class);
        for ($i=0,$j=count($class);$i<$j;$i++){
            $class[$i]=str_replace("'",'',$class[$i]);
        }
        for($i=0,$j=count($class[0]);$i<$j;$i++){
            if(strstr($class[0][$i],'@')){
                $data[]=explode('@',$class[0][$i]);
            }
        }
        foreach ($data as $k=>$v){
            $info[$k]['class']=$v[0];
            $info[$k]['method']=$v[1];
            $info[$k]['method_code']=$k+1;
        }
        $key= array_keys(collect($info)->groupBy('class')->toArray());
        foreach ($key as $k=>$v){
            $newData[$k+1]=$v;
        }
        foreach ($info as $k=>$v){
            $info[$k]['class_code']=array_flip($newData)[$v['class']];
        }
        return $info;
    }

    public function test(){
        $data=0;
        $arr=['a','b','c'];
        for($i=0,$j=count($arr);$i<$j;$i++){
            $data=$data+1;
        }
        print_r($data);
    }
}

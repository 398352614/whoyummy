<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;

class SidebarController
{
    public int $level = 0;

    public array $array = [

    ];

    public string $md = '';
    private array $all = [];

    public function sidebar(Request $request)
    {
        $path = "$request->path";  // 需要转换的文件路径。
        $this->get_filenamesbydir($path);
        file_put_contents(storage_path($path . '/_sidebar.md'), $this->md);

    }

    public array $list = [];

    function get_allfiles($path, &$files, &$stringArr)
    {
        if (is_dir($path)) {
            $this->level++;
            $dp = dir($path);
            while ($file = $dp->read()) {
                if ($file !== "." && $file !== "..") {
                    $this->get_allfiles($path . "/" . $file, $files, $stringArr);
                }
            }
            $dp->close();
        } elseif (is_file($path)) {
            $this->pushDepth($path);
        }
    }

    public function pushDepth($path)
    {
        $dir = explode('/', $path);
        foreach ($dir as $k => $v) {
            if (!in_array($v, $this->all[$k] ?? [])) {
                $space = '';
                for ($i = 0, $j = $k; $i < $j; $i++) {
                    $space .= '  ';
                }
                if (strstr($v, '.md')) {
                    $this->md .= $space . '- [' . str_replace('.md', '', $v) . '](/' . $path . ")\r\n";
                } else {
                    $this->md .= $space . '- ' . $v . "\r\n";
                }
                $this->all[$k][] = $v;
            }
        }
    }

    public function form($arr)
    {
    }


    function get_filenamesbydir($dir)
    {
        $files = $stringArr = array();
        $this->get_allfiles($dir, $files, $stringArr);
        return $files;
    }
}

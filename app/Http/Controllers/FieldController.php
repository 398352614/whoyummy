<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function Composer\Autoload\includeFile;

class FieldController extends Controller
{
    public function field(Request $request)
    {
        $txt = "";
        $path = "$request->path";  // 需要转换的文件路径。
        $toPath = "$request->toPath";  // 最终要放到的位置。
        $txtName = "$request->txtName";//文件名。

        $stringArr = $this->get_filenamesbydir($path);
        $fieldList = $this->getFieldList($stringArr);
        $enList = $this->getLanguage('en');
        $cnList = $this->getLanguage('zh-CN');
        foreach ($fieldList as $k => $field) {
            $fieldList[$k]['cn_name'] = '';
            $fieldList[$k]['en_name'] = '';
            foreach ($field['key_name'] as $y) {
                $fieldList[$k]['cn_name'] .= $cnList[$y] ?? $y;
                $fieldList[$k]['en_name'] .= $enList[$y] ?? $y;
            }
            $fieldList[$k]['is_visible'] = 1;
            unset($fieldList[$k]['key_name']);
        }
        $fieldList = json_encode($fieldList, 256);

        file_put_contents($txtName . '.json', $fieldList);
        return $fieldList;

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
        } elseif (is_file($path)) {
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

    public function getLanguage($language)
    {
        $data = file_get_contents('i18n/' . $language . '.js');
        preg_match_all("/{[\s\S]*}/", $data, $y);
        $y = $y[0][0];
        $y = str_replace("\n\n", '\n', $y);

        $y = preg_replace("/\/\/[\s\S]*?\n/", '', $y);
        $y = str_replace("'", '"', $y);

        $y = str_replace(': "', '":"', $y);

        $y = str_replace(',', ',"', $y);
        $y = str_replace("{\n", "{\n" . '"', $y);
        $y = str_replace('"' . "\n", "\n" . '"', $y);
        $y = str_replace('"' . "\n" . '}', '}', $y);
        preg_match_all("/\n\"[\s\S]*?\":\"[\s\S]*?\",/", $y, $z);
        $z = $z[0];
        foreach ($z as $k => $v) {
            $z[$k] = str_replace("\n" . '"', '', $v);
            $z[$k] = str_replace('",', '', $z[$k]);
            $a = explode('":"', $z[$k]);
            $enList[trim($a[0])] = $a[1];
        }
        return $enList;
    }

    private function getFieldList(array $stringArr)
    {
        foreach ($stringArr as $name => $content) {

            preg_match_all("/<el-table[\s\S]*?<\/el-table>/", $content, $x[$name]);
            foreach ($x[$name][0] as $k => $v) {
                preg_match_all("/<el-table-column[\s\S]*?>/", $content, $data[$name]);

            }
        }
        foreach ($data as $k => $v) {
            if (strstr($k, 'List')) {
                $newData[$k] = $v[0];
                foreach ($newData[$k] as $x => $y) {
                    $y = str_replace('<el-table-column', '', $y);
                    $y = str_replace('=', ':', $y);
                    $y = str_replace(array("\r\n", "\r", "\n"), ',', $y);
                    $y = str_replace('>', '', $y);
                    $y = str_replace(' ', '', $y);
                    $y = substr($y, 0, strlen($y) - 1);
                    $y = '{' . $y . '}';
                    $y = str_replace('{,', '{', $y);
                    $y = str_replace(':', '":', $y);
                    $y = str_replace(',', ',"', $y);
                    $y = str_replace('{', '{"', $y);
                    $y = str_replace('},', '"}', $y);
                    $y = str_replace('""', '"', $y);
                    $result[$k][$x] = json_decode($y);
                }
            }
        }
        foreach ($result as $k => $v) {
            //去除不需要的字段
            foreach ($v as $x => $y) {
                $y = collect($y)->toArray();
                if (empty($y['prop']) || empty($y[':label'])) {
                    unset($v[$x]);
                }
            }
            $v = array_values($v);
            foreach ($v as $x => $y) {
                $y=collect($y)->toArray();
                preg_match_all("/\('[\s\S]*?'\)/", $y[':label'], $z);
                foreach ($z as $a => $b) {
                    $z[$a] = str_replace("('", '', $z[$a]);
                    $z[$a] = str_replace("')", '', $z[$a]);
                }
                $newResult[] = [
                    'sort_id' => $x+1,
                    'key' => $y['prop'],
                    'table' => $k,
                    'key_name' => $z[0]
                ];
            }
        }
        return $newResult;
    }
}



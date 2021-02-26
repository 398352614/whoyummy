<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;

class JsonToMarkdownController
{
    public function jsonToMarkDown(Request $request)
    {
        $path = "$request->path";  // 需要转换的文件路径。
        $toPath = "$request->toPath";  // 最终要放到的位置。
        $txtName = "$request->txtName";//文件名。
        $json = file_get_contents($path);
        $md = $this->form($json);
        $a = $toPath . $txtName . time() . '.md';
        $file = fopen($a, "x+");
        $row = file_put_contents($a, $md);
        fclose($file);
        if (!empty($row)) {
            return 'success';
        }
        return 'fail';
    }

    public array $dictionary;

    public function form($json)
    {
        $md = json_decode($json);
        $newMd = "# 项目名：" . $md->info->name . "  \r\n" . "## 接口" . "  \r\n";
        //获取字典
        foreach ($md->item as $a => $b) {
            foreach ($b->item as $z => $request1) {
                if (!empty($request1->response[0]->originalRequest)) {
                    $request1 = $request1->response[0]->originalRequest;
                } else {
                    $request1 = $request1->request;
                }

                if (!empty($request1->body->urlencoded)) {
                    foreach ($request1->body->urlencoded as $y => $fields) {
                        if (empty($this->dictionary[$fields->key])) {
                            $this->dictionary[$fields->key] = $fields->description ?? '';
                        }
                    }
                }
            }
        }
        //未递归，只支持二级结构
        foreach ($md->item as $k => $v) {
            $newMd = $newMd . "### " . ($k + 1) . " " . $v->name . "  \r\n";
            foreach ($v->item as $x => $request) {
                $newMd = $newMd . "#### " . ($k + 1) . "." . ($x + 1) . " " . $request->name . "  \r\n";
                if ($request->request->method == 'GET') {
                    $newMd = $this->get($request->request, $newMd);
                } else {
                    $newMd = $this->post($request, $newMd);
                }
                $newMd = $this->response($request, $newMd);
            }
        }
        return $newMd;
    }

    public function get($request, $newMd)
    {
        if (!empty($request->url->raw)) {
            $url = $request->url->raw;
        } else {
            $url = $request->url;
        }
        $newMd = $newMd . "请求地址: " . $url . "  \r\n";
        $newMd = $newMd . "请求方式: " . $request->method . "  \r\n";
        $newMd = $newMd . "请求参数:  \r\n\r\n";
        $newMd = $newMd . "|参数名|说明|必填|示例|  \r\n |---|---|---|---|  \r\n";
        if (!empty($request->url->query)) {
            foreach ($request->url->query as $y => $fields) {
                if (!empty($fields->key)) {
                    if (empty($fields->description)) {
                        $fields->description = $this->dictionary[$fields->key] ?? '';
                    }
                    if ($fields->description == 'XDEBUG_SESSION_START') {
                        continue;
                    }
                    $fields->description = '';
                    $newMd = $newMd . "|" . $fields->key . "|" . $fields->description . "|是|" . $fields->value . "  \r\n";
                    $this->dictionary[] = [$fields->key => $fields->description];
                }
            }
        }
        return $newMd;
    }

    public function post($request1, $newMd)
    {
        //优先读示例
        if (!empty($request1->response[0]->originalRequest)) {
            $request = $request1->response[0]->originalRequest;
        } else {
            $request = $request1->request;
        }
        if (!empty($request->url->raw)) {
            $url = $request->url->raw;
        } else {
            $url = $request->url;
        }
        $newMd = $newMd . "请求地址: " . $url . "  \r\n";
        $newMd = $newMd . "请求方式: " . $request->method . "  \r\n";
        $newMd = $newMd . "请求参数:  \r\n\r\n";
        $newMd = $newMd . "|参数名|说明|必填|示例|  \r\n |---|---|---|---|  \r\n";
        if (!empty($request->body->urlencoded)) {
            foreach ($request->body->urlencoded as $y => $fields) {
                if (empty($fields->description)) {
                    $fields->description = $this->dictionary[$fields->key] ?? '';
                }
                $newMd = $newMd . "|" . $fields->key . "|" . $fields->description . "|是|" . $fields->value . "  \r\n";
                $this->dictionary[] = [$fields->key => $fields->description];
            }
        }
        return $newMd;
    }

    public function response($request1, $newMd)
    {
        $newMd = $newMd . "返回参数  \r\n\r\n";
        $newMd = $newMd . "|参数名|说明|必含|示例|  \r\n |---|---|---|---|  \r\n";
        //优先读示例
        if (!empty($request1->response[0])) {
            $response = $request1->response[0]->body;
            $response = collect(json_decode($response))->toArray();
            if (!empty($response)) {
                foreach ($response as $key => $value) {
                    $description = $this->dictionary[$key] ?? '';
                    if (is_object($value)) {
                        $newMd = $newMd . "|" . $key  . "|" . $description . "|是|" . " " . "  \r\n";
                        $value = collect($value)->toArray();
                        foreach ($value as $a => $b) {
                            if (is_object($b)) {
                                $b = json_encode($b);
                            }
                            $newMd = $newMd . "|" . $key . "." . $a . "|" . $description . "|是|" . $b . "  \r\n";
                        }
                    } else {
                        $newMd = $newMd . "|" . $key . "|" . $description . "|是|" . $value . "  \r\n";
                    }
                }
            }
        }
        return $newMd;
    }

}

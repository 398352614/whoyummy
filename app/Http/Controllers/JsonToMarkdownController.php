<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;

class JsonToMarkdownController
{

    public array $description = [];

    public int $level = 0;

    public function jsonToMarkDown(Request $request)
    {
        $path = "$request->path";  // 需要转换的文件路径。
        $json = json_decode(file_get_contents(storage_path($path)));
        $this->recursion($json, '');
    }

    public array $dictionary;

    public function recursion($json, $path)
    {
        if (!empty($json->item[0])) {
            if (empty($json->name)) {
                $name = $json->info->name . '/';
            } else {
                $name = $json->name . '/';
            }
            $path .= $name;
            if (!file_exists(storage_path($path))) {
                mkdir(storage_path($path), 0777, true);
            }
            foreach ($json->item as $v) {
                $this->recursion($v, $path);
            }
        } else {
            $this->makeMd($json, $path);
        }
    }

    public function makeMd($params, $path)
    {
        $params->name = str_replace('/', '-', $params->name);
        file_put_contents(storage_path($path . $params->name . '.md'), $this->getApi($params));
    }

    public function getApi($params)
    {
        if (!empty($params->response[0]->originalRequest)) {
            $request = $params->response[0]->originalRequest;
        } elseif (!empty($params->request)) {
            $request = $params->request;
        } else {
            return '';
        }
        $md = '## ' . $params->name . "\r\n";
        $md .= "### 请求地址:\r\n";
        $md .= "```\r\n" . $this->getUrl($request) . "\r\n```\r\n";
        $md .= "### 请求方式: " . $request->method . "  \r\n";
        $md .= "### 请求参数:  \r\n\r\n";
        $md .= "|参数名|说明|必填|示例|  \r\n |---|---|---|---|  \r\n";
        $md .= $this->getRequest($request);
        $md .= "### 返回参数  \r\n\r\n";
        $md .= "|参数名|说明|必填|示例|  \r\n |---|---|---|---|  \r\n";
        $md .= $this->getResponse($params);
        return $md;
    }

    public function getUrl($request)
    {
        if (!empty($request->url->raw)) {
            $url = $request->url->raw;
        } elseif (!empty($request->url)) {
            $url = $request->url;
        } else {
            return '';
        }
        return preg_replace('/\/[0-9]+/', "/{id}", $url);
    }

    public function getRequest($request)
    {
        $md = '';
        if (!empty($request->method->query)) {
            foreach ($request->url->query as $fields) {
                if (!empty($fields->key)) {
                    $this->addData($fields->key, $fields->description ?? '', $fields->value ?? '', $md);
                }
            }
        }
        if (!empty($request->body)) {
            $mode = $request->body->mode;
            if ($mode == 'raw') {
                $list = collect(json_decode($request->body->raw))->toArray();
                foreach ($list as $key => $value) {
                    $this->addData($key, '', $value, $md);
                }
            } else {
                foreach ($request->body->$mode as $fields) {
                    if (!empty($fields->key)) {
                        $this->addData($fields->key, $fields->description ?? '', $fields->value ?? '', $md);
                    }
                }
            }
        }
        return $md;
    }

    public function addData($key, $description, $value, &$md)
    {
        if (is_object($value)) {
            $this->addRow($key, $description, '', $md);
            foreach ($value as $k => $v) {
                $this->addData($key . '.' . $k, '', $v ?? '', $md);
            }
        } else {
            $this->addRow($key, $description, $value ?? '', $md);
        }
    }

    public function addRow($key, $description, $value, &$md)
    {
//        $description = $this->getDescription($key, $description);
        $md .=
            "|" . $key .
            "|" . json_encode($description) .
            "|是" .
            "|" . json_encode($value) .
            "|  \r\n";
//        $this->addDescription($key, $description);
    }

    public function addDescription($key, $description)
    {
        $this->description[$key] = $description;
    }

    public function getDescription($key, $description)
    {
        if (empty($description)) {
            $this->description[$key] = $description;
        }
        return $description;
    }

    public function getResponse($params)
    {
        $md = '';
        //读示例
        if (!empty($params->response)) {
            $response = $params->response[0]->body;
            $response = collect(json_decode($response))->toArray();
            if (!empty($response)) {
                foreach ($response as $key => $value) {
                    $this->addData($key, '', $value, $md);
                }
            }
            $md .= "### 返回示例" . "\r\n" . '```json' . "\r\n" . $params->response[0]->body . '```' . "\r\n";
        }
        return $md;
    }

}

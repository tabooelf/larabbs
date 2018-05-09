<?php

namespace App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;


class SlugTranslateHandler{

    public function translate($text){
        // 实例化 HTTP 客户端
        $http = new Client;

        // 初始化配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid;
        $key;
        $salt = time();

        // 如果没有配置百度翻译,自己使用兼容的拼音方案
        if(empty($appid) || empty($key)){
            return $this->pinyin($text);
        }

        // 生成签名
        $sign = md5($appid + $text + $salt + $key);

        // 构建请求参数
        $query = http_build_query([
            'q' => $text,
            'form' => 'zh',
            'to' => 'en',
            'appid' => $appid,
            'salt' => $salt,
            'sign' => $sign,
        ]);

        /**
        获取结果，如果请求成功，dd($result) 结果如下：
        array:3 [▼
            "from" => "zh"
            "to" => "en"
            "trans_result" => array:1 [▼
                0 => array:2 [▼
                    "src" => "XSS 安全漏洞"
                    "dst" => "XSS security vulnerability"
                ]
            ]
        ]
        **/

        // HTTP发送请求并返回
        $response = $http->get($api.$query);
        $result = json_decode($response->getBody(), true);

        if(isset($result['trans_result'][0]['dst'])){
            return str_slug($result['trans_result'][0]['dst']);
        } else {
            return $this->pinyin($text);
        }
    }

    public function pinyin($text){
        return str_slug(app(Pinyin::class)->permalink($text));
    }
}
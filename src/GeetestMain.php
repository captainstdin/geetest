<?php

namespace Geetest;

use Geetest\CurlCore;

class GeetestMain
{
    public $domain = 'api.geetest.com';
    public $protocol = 'http://';



    protected $gt='';
    /**
     * @var CurlCore
     */
    public $curl;

    public function __construct(string $gt,string $se)
    {
        $this->curl = new CurlCore($this->protocol . $this->domain);
        $this->gt=$gt;
    }

    public $client_type='web';
    public $sdk='qqfirst/geetest/composer';

    /**
     * @param string $client_type 客户端类型，web（pc浏览器），h5（手机浏览器，包括webview），native（原生app），unknown（未知）
     * @param string|null $ip
     * @return string
     * @throws \Exception
     */
    public function register(string $ip=null):string
    {

        $query = [
            'user_id' => '',
            'client_type' => $this->client_type,
            'ip_address' => $ip,
            'digestmod' => 'md5',
            'gt' => $this->gt,
            'json_format' => '1',
            'sdk' => 'php'
        ];

        $query=http_build_query($query);
        $res = $this->curl->set_uri('/register.php?'.$query)
            ->set_header(['Content-Type: application/x-www-form-urlencoded'])
            ->post('get');


        $array=  $res->get_body();
        if(!isset($array['challenge'])){
            throw new \Exception('验证生成失败！无法确定challenge。');
        }
        return $array['challenge'];
    }


    public function validate(string $seccode,string $challenge,string $ip=null)
    {
        $query = [
            'user_id' => '',
            'client_type' => $this->client_type,
            'ip_address' => $ip,
            'seccode' => $seccode,//核心校验数据
            'challenge'=>$challenge,
            'json_format'=>'1',
            'sdk'=>'',
            'captchaid'=>$this->gt
        ];

        $res = $this->curl->set_uri('/validate.php')
            ->set_post($query,true)
            ->set_header(['Content-Type: application/x-www-form-urlencoded'])
            ->post('post');

        $array=  $res->get_body();
        if(!isset($array['seccode'])){
            throw new \Exception('验证生成失败！无法确定seccode。');
        }
        return $array['seccode'];
    }


}
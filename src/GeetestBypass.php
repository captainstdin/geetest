<?php

namespace Geetest;

class GeetestBypass
{
    public $domain = 'bypass.geetest.com';
    public $protocol = 'https://';

    public function __construct(string $gt,string $se)
    {
        $this->curl = new CurlCore($this->protocol . $this->domain);
        $this->gt=$gt;
    }

    /**
     * @param  string $gt
     * @return bool
     * @throws \Exception
     */
    public function check(string $gt):bool
    {
        $res = $this->curl->set_uri('/v1/bypass_status.php')
            ->set_header(['Content-Type: application/x-www-form-urlencoded'])
            ->set_post(['gt'=>$gt,],true)
            ->post('post');

        $array=  $res->get_body();
        if(!isset($array['status'])){
            throw new \Exception('验证生成失败！无法确定status。');
        }

        return $array['status']=="success";
    }

}
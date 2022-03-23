<?php

namespace Geetest;

use Geetest\CurlCore;

class GeetestMain
{
    public $domain = 'api.geetest.com';
    public $protocol = 'http://';

    protected $key = '';
    protected $gt = '';

    protected $passby = false;
    /**
     * @var CurlCore
     */
    public $curl;

    public function __construct(string $gt, string $se)
    {
        $this->curl = new CurlCore($this->protocol . $this->domain);
        $this->gt = $gt;
        $this->key = $se;
    }

    public $client_type = 'web';
    public $sdk = 'qqfirst/geetest/composer';

    public function getPassby()
    {
        return $this->passby;
    }

    /**
     * @param string $client_type 客户端类型，web（pc浏览器），h5（手机浏览器，包括webview），native（原生app），unknown（未知）
     * @param string|null $ip
     * @return string
     * @throws \Exception
     */
    public function register(string $ip = null): string
    {
        $pre = new GeetestBypass($this->gt, $this->key);
        if (!$pre->check()) {
            $this->passby = false;
            return $this->registerLocal();
        }
        $this->passby = true;
        return $this->registerGeetest($ip);
    }


    private function registerLocal(): string
    {
        $rnd1 = \md5(\rand(0, 100));
        $rnd2 = \md5(\rand(0, 100));
        $challenge = $rnd1 . \substr($rnd2, 0, 2);
        return $challenge;
    }

    private function registerGeetest(string $ip = null): string
    {
        $query = [
//            'user_id' => '',
            'client_type' => $this->client_type,
            'ip_address' => $ip,
            'digestmod' => 'md5',
            'gt' => $this->gt,
            'json_format' => '1',
            'sdk' => $this->sdk,
            'new_captcha' => '1'
        ];

        $query = http_build_query($query);
        $res = $this->curl->set_uri('/register.php?' . $query)
            ->set_header(['Content-Type: application/x-www-form-urlencoded'])
            ->post('get');

        $array = $res->get_body();
        if (!isset($array['challenge'])) {
            throw new \Exception('验证生成失败！无法确定challenge。');
        }

        return \md5($array['challenge'] . $this->key);
    }


    /**
     * @param bool $status true为走在线校验，false为离线校验
     * @param string $validate 来自客户端geetest
     * @param string $seccode 来自客户端geetest
     * @param string $challenge 来自客户端geetest
     * @param string|null $ip
     * @return bool
     */
    public function validate(bool $status, string $validate, string $seccode, string $challenge, string $ip = null)
    {
        if (!$status) {
            return $this->validateLocal($challenge,$validate);
        }

        return $this->validateGeetest($seccode,$challenge,$ip);
    }


    public function validateLocal(string $challenge,string $validate):bool
    {
        return \md5($challenge) == $validate;
    }

    public function validateGeetest(string $seccode,string $challenge,string $ip=null){
        $query = [
            'user_id' => '',
            'client_type' => $this->client_type,
            'ip_address' => $ip,
            'seccode' => $seccode,//核心校验数据
            'challenge' => $challenge,
            'json_format' => '1',
            'sdk' => '',
            'captchaid' => $this->gt
        ];

        $res = $this->curl->set_uri('/validate.php')
            ->set_post($query, false)
            ->set_header(['Content-Type: application/x-www-form-urlencoded'])
            ->post('post');

        $array = $res->get_body();

        if (!isset($array['seccode'])) {
            return false;
        }

        if(\strval($array['seccode'])=="false"){
            return false;
        }
        return true;
    }

}
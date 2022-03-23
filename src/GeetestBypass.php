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
     */
    public function check():bool
    {
        $res = $this->curl->set_uri('/v1/bypass_status.php')
            ->set_header(['Content-Type: application/x-www-form-urlencoded'])
            ->set_post(['gt'=>$this->gt,])
            ->post('post');

        if(!$res->is_error()){
            return false;
        }
        $array=  $res->get_body();
        if(!isset($array['status'])){
            return false;
        }
        return $array['status']=="success";
    }

}
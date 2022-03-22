<?php

namespace Qqfirst\Geetest;

class CurlCore
{
    public $timeout = 10;

    public $uri = '';

    public $header = [];

    public $post ;

    public $body = [];

    public $raw_body = '';

    public $msg = '未知';

    public $status = 0;

    public $url='';

    public function __construct(string $domain = '')
    {
        if ($domain != '') {
            $this->url=$domain;
        }
//        $this->set_header(['Content-Type:application/json']);
    }



    public function get_status(): int
    {
        // TODO: Implement get_status() method.
        return $this->status;
    }


    public function is_error(bool $throw = false): bool
    {
        // TODO: Implement is_error() method.
        if( substr(strval($this->status),0,1)=='2' ){
            return true;
        }
        if($throw){
            throw new \Exception('error:'.$this->get_msg(),$this->status);
        }
        return false;
    }

    public function get_msg()
    {
        // TODO: Implement get_msg() method.
        if(isset($this->body['message'])){
            $this->msg=$this->body['message'];
        }
        return $this->msg;
    }

    public function get_body(bool $raw = false)
    {
        // TODO: Implement get_body() method.
        if ($raw) {
            return $this->raw_body;
        }
        return $this->body;
    }

    public function set_uri(string $uri = ''): self
    {
        // TODO: Implement set_uri() method.
        if ($this->url != '') {
            if(substr($uri,0,1)=='/'){
                $uri=substr($uri,1);
            }
            $this->uri = $uri;
        }
        return $this;
    }

    public function set_header(array $array = []): self
    {
        // TODO: Implement set_header() method.
        $this->header = $array;
        return $this;
    }

    public function set_post(mixed $array ,$raw=false): self
    {
        // TODO: Implement set_post() method.
        if($raw){
            $this->post = $array;
        }else{
            $this->post='';

            foreach ($array as $k=>$v){
                if(\is_array($v)){
                    $v=\json_encode($v);
                }
                $this->post.=$k.'='.$v.'&';
            }
            $this->post=trim($this->post,'&');
        }
        return $this;
    }

    public function post(string $method='get')
    {
        $this->status=0;
        // TODO: Implement post() method.
        $_methods = [
            'put', 'delete', 'get', 'post'
        ];
        if(!in_array($method,$_methods)){
            throw new \Exception('不支持方式请求');
        }

        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);


        //不直接输出
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        \curl_setopt($ch, CURLOPT_URL, $this->url.'/'.$this->uri);
        \curl_setopt($ch, CURLOPT_HEADER, 0);
        \curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        \curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        \curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post);

        $this->raw_body=\curl_exec($ch);


        //获取代码code
        $this->status = \curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //如果超时
        if (\curl_errno($ch) == CURLE_OPERATION_TIMEDOUT || \curl_errno($ch) == 7 || $this->status == 0) {
            //超时代码
            $this->status = 0;
            $this->msg=\curl_errno($ch).'|系统超时故障!请管理员,检查TCP通讯是否正常。';
        }
        \curl_close($ch);
        $this->body=\json_decode($this->raw_body,true);
        return $this;
    }



}
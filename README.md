## geetest 极验 PHP后端

### 使用方法

### 初始化参数 (webman下使用)

```php
public function geetestInit(Request $request)
{
    /**
     * @var
     * GeetestMain
     */
    $c = Container::make(GeetestMain::class, [Env::get('geetest.id'), Env::get('geetest.key')]);
    $challenge=$c->register($request->getRealIp(true));
    //记录到用户，本次验证的geet服务器状态。
    $request->session()->set('geetest_server_status', $c->getPassby());
    return json([
        'code' => 0,
        'geetest_id'=>Env::get('geetest.id'),
        'geetest_status'=>$c->getPassby()?1:0,
        'challenge' => $challenge
    ]);
}
```

###  验证前端发送来的数据(webman例子)

```php
public function geetestValidate(Request $request)
{
    /**
     * @var
     * GeetestMain
     */
    $c = Container::make(GeetestMain::class, [Env::get('geetest.id'), Env::get('geetest.key')]);

    $seccode=$request->post('geetest_seccode');
    $validate=$request->post('geetest_validate');
    $challenge=$request->post('geetest_challenge');


    $geet_status=$request->session->get('geetest_server_status',false);
    $seccode = $c->validate($geet_status,$validate,$seccode,$challenge,$request->getRealIp(true));

    if(strtolower(strval($seccode))=='false'){
        return  json([
            'code'=>1,
            'msg'=>'验证未通过，请重新验证。'
        ]);
    }
    $request->session()->set('geetest_status',true);
    return json([
        'code' => 0,
        'seccode' =>$seccode
    ]);
}
```


## 前端代码（Vue-cli）

```vue
<a-form-item label="AI校验">
  <div ref="geetest"></div>
</a-form-item>

```

```vue
<script >
export default {
  methods:{
    GeetestOnReady(){
      this.geetest_init=true
    },

    GeetestOnSuccess(){
      let a=this.captchaObj.getValidate();
      if(a==false){
        return false;
      }

      this.$ajax.postJson('/web/HomeLogin/geetestValidate',a).then(e=>{
        console.log(e);
        //验证通过，飞人机

      }).catch(()=>{
        this.captchaObj.reset();
      })
    },

    GeetestOnError(){
    },
    GeetestHandle(captchaObj){
      this.captchaObj=captchaObj
      captchaObj.appendTo(this.$refs.geetest);
      // 这里可以调用验证实例 captchaObj 的实例方法
      captchaObj.onReady(this.GeetestOnReady)
      captchaObj.onSuccess(this.GeetestOnSuccess)
      captchaObj.onError(this.GeetestOnError)
    },
    GeetestInit(){
      this.$ajax.postJson("/web/HomeLogin/geetestInit").then(e=>{
        //请检测data的数据结构， 保证data.gt, data.challenge, data.success有值
        window.initGeetest({
          // 以下配置参数来自服务端 SDK
          gt: e.geetest_id,
          challenge: e.challenge,

          width: '100%',
          new_captcha: true,
          https: true,
          offline:!e.geetest_status
        },this.GeetestHandle)
      });
    }
  }
}
</script>
```
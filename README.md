## geetest 极验 PHP后端

### 使用方法

### 初始化参数

```php
<?php
require_once "vendor/autoload.php";
//########init geetest
$a=new \Qqfirst\Geetest\GeetestMain('cf94327051******088b3241','42a335******eecc3');
$challege= $a->register('web');
```

###  验证前端发送来的数据

```php
<?php
require_once "vendor/autoload.php";
//########init geetest
$a=new \Qqfirst\Geetest\GeetestMain('cf94327051******088b3241','42a335******eecc3');
//######### validate
$seccode= $a->validate('aasdasd*****',$challege);
```


### (可选) 检测geetest服务器
```php
//### check healthy
$a=new \Qqfirst\Geetest\GeetestBypass('cf94327051******088b3241','42a335******eecc3');
$a->check('xxxxxxxxx');
```
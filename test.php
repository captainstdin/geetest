<?php
require_once "vendor/autoload.php";
//########init geetest

$a=new \Qqfirst\Geetest\GeetestMain('cf94327051******088b3241','42a335******eecc3');

$challege= $a->register('web');

//######### validate
$seccode= $a->validate('aasdasd*****',$challege);


//### check healthy
$a=new \Qqfirst\Geetest\GeetestBypass('cf94327051******088b3241','42a335******eecc3');
$a->check('xxxxxxxxx');




<?php
require_once "vendor/autoload.php";
//########init geetest

$a=new \Geetest\GeetestMain('cf94327051******088b3241','42a335******eecc3');

$challege= $a->register('web');

//######### validate
$seccode= $a->validate(true,'aasdasd*****',$challege);


//### check healthy
$a=new \Geetest\GeetestBypass('cf94327051******088b3241','42a335******eecc3');
$a->check('xxxxxxxxx');




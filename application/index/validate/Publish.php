<?php
namespace app\index\validate;
use \think\Validate;
class Publish extends Validate
{
    // 当前验证的规则
     protected $rule = [
	'name'=>'require','owner'=>'require','description'=>'require','price'=>'require','owner_id'=>'require','path'=>'require',

	];

	protected $message = [
	'name.require' => '用户名必须',
	'owner'=> '您的登录存在问题',
	'owner_id' => '您的登录存在问题',
	'description'=>'商品描述必须',
	'price'=>'价格必须',
	'path'=>'商品图存在问题'
	];

}

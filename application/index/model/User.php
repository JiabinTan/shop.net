<?php
namespace app\index\model;
use \think\Model;

class User extends Model
{
	protected $autoWriteTimestamp='timestamp';
	protected $table = 'user';
}
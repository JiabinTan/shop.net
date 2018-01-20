<?php
namespace app\index\controller;
use \think\Controller;
use \think\Session;
use \think\Cookie;
use \app\index\model\Index as IndModel;
class Publish extends Controller
{
	public function check(){
	$session_id=Session::get('id','personinfo');
	$session_username=Session::get('username','personinfo');
	if($session_id&&$session_username)
		return json(['status'=>'ok']);
	else {
		 	return json(['status'=>'fail']);
		}
	}
	public function index()
	{
		return $this->fetch();
	}
	public function receive()
	{
		$image = request()->file()['fileList'];
		dump($image);
		$info = $image->move(ROOT_PATH . 'public/'  . 'uploads');
		
		
	}
}
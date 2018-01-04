<?php
namespace app\index\controller;
use \think\Controller;
use \think\Cookie;
use \think\Session;
use \think\Request;
use \app\index\model\Login as LogModel;
class Login extends Controller
{
	
    public function check()
    {
		$log=new LogModel();
		$req=Request::instance();
		$data=$req->param();
		$autolog_token=MD5(time());
		$info=$log->get(["username"=>$data["username"]]);
		if($info==null)
			return json(['status'=>'fail_user','messege'=>"用户名不存在"]);
		$id=$info->id;
		$pass=$info->password;
		$passcomfirm=md5($data['password']);
		
		
		if($pass==$passcomfirm)
		{
			Session::set('id',$id,'personinfo');
			Session::set('username',$info->username,'personinfo');
			if(array_key_exists("RemPSW",$data))
				if($data['RemPSW']=='on')
					{
						$info->autolog_token=$autolog_token;
						$info->save();
						cookie::set("autolog_username",$data['username'],604800);
						cookie::set("autolog_token",$autolog_token,604800);
						
					}
			return json(['status'=>'ok','username'=>$data["username"],'id'=>$id]);
		}
		else
			return json(['status'=>'fail_pass','messege'=>"密码错误"]);

	}
}

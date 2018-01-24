<?php
namespace app\index\controller;
use \think\Controller;
use \think\Session;
use \think\Cookie;
use \app\index\model\Index as IndModel;
class Index extends Controller
{
    public function index()
    {
        return $this->fetch("MainPage");
    }
	public function login()
	{
		return $this->fetch('common/login/login'); 
	}
	public function logout()
	{
		Cookie::clear('autolog_');
		Session::clear("personinfo");
		$url=url("\\");
		echo $url;
		header("Location:$url");
	}
	public function getSession()
	{
		$autolog_username=Cookie::get('autolog_username');
		$autolog_token=Cookie::get("autolog_token");
		$session_id=Session::get('id','personinfo');
		$session_username=Session::get('username','personinfo');
		if($session_id&&$session_username)
		{
			return json(['status'=>'ok','username'=>$session_username,'id'=>$session_id]);
		}
		else if($autolog_username&&$autolog_token)
			{
				$ind=new IndModel();
				$info=$ind->get(["username"=>$autolog_username]);
				if($info['autolog_token']==$autolog_token)
				{
					Session::set('id',$info['id'],'personinfo');
					Session::set('username',$info['username'],'personinfo');
			
					return json(['status'=>'ok','username'=>$info['username'],'id'=>$info['id']]);
				}
				else
					return json(['status'=>'fail']);
			}
			
		else
		{
			return json(['status'=>'fail']);
		}
	}
	public function jump()
	{
		return $this->fetch("jump");
	}

}

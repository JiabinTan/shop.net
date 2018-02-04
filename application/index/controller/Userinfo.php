<?php
namespace app\index\controller;
use \think\Controller;
use \think\Session;
use \think\Cookie;
use \think\Request;
use phpmailer\PHPMailer;
use \app\index\model\User as UserModel;
use \app\index\model\Goods as GoodsModel;
class Userinfo extends Controller
{
    public function index()
    {
        return $this->fetch("userinfo");
    }
	public function getInfo(){
		$flag=0;//是否存在商品已经删除
		$user=new UserModel();
		$goods=new GoodsModel();
		$session_id=Session::get('id','personinfo');
		$session_username=Session::get('username','personinfo');
		if(!($session_id&&$session_username))
			return json(["status"=>"fail","messege"=>"您的登录存在问题","id"=>$session_id,"user"=>$session_username]);
		$userinfo=$user->field("id,username,email,contact,school,headshot,ip,likes,purchased,email_state")->find($session_id);
		if(!$userinfo)
			return json(["status"=>"fail","messege"=>"您的登录存在问题,无法获得对应信息"]);
		$likes=explode(";",$userinfo->likes);
		$purchase=explode(";",$userinfo->purchased);

		
		$likes_count=count($likes);
		$purchase_count=count($purchase);
		$likesinfo=Array();
		$purchaseinfo=Array();
		if($likes_count>1)
			for($i=1,$j=1;$i<$likes_count;$i++,$j++)
			{
				$temp=$goods->field("expire,name,owner,price,id")->find($likes[$i]);
				if(!$temp)
				{
					unset($likes[$i]);
					$flag=1;
					$j--;
					continue;
				}
				if(strtotime($temp->expire)<time())
				{
					unset($likes[$i]);
					$flag=1;
					$j--;
					continue;
				}
				$likesinfo[$j-1]=$temp;
			}
		else
			$likesinfo=null;

			if($flag)
			{
				$userinfo->likes=implode(";",$likes);
				$flag=0;
				if(!$userinfo->save())
					return json(["status"=>"fail","messege"=>"1数据保存错误！"]);
			}
		if($purchase_count>1)
			for($i=1,$j=1;$i<$purchase_count;$i++,$j++)
			{
			$temp=$goods->field("expire,name,owner,price,id")->find($purchase[$i]);
				if(!$temp)
				{
					unset($purchase[$i]);
					$flag=1;
					$j--;
					continue;
				}
				if(strtotime($temp->expire)<time())
				{
					unset($purchase[$i]);
					$flag=1;
					$j--;
					continue;
				}
				$purchaseinfo[$j-1]=$temp;
			}
		else
			$purchaseinfo=null;
		

		if($flag)
			{
				$userinfo->purchased=implode(";",$purchase);
				$flag=0;
				if(!$userinfo->save())
					return json(["status"=>"fail","messege"=>"数据保存错误！"]);
			}
		$goodinfo=$goods->where("owner_id",$session_id)->where('expire','>=',date('Y-m-d H:i:s'))->field("name,expire,price,id")->select();
		$data=["userinfo"=>$userinfo->toJson(),"goodsinfo"=>json_encode($goodinfo),"likesinfo"=>json_encode($likesinfo),"purchaseinfo"=>json_encode($purchaseinfo)];
		return json(['status'=>"ok","data"=>$data]);
	}
	public function delete()
	{
		$para=Request::instance()->param();
		if(!array_key_exists("id",$para)||!array_key_exists("sel",$para))
			return json(["status"=>"fail","messege"=>"数据丢失！"]);
		$good_id=$para["id"];
		$sel=$para["sel"];
		$user_id=Session::get("id","personinfo");
		if(!$user_id)
			return json(["status"=>"fail","messege"=>"请先登录后再继续操作！"]);
		$user_model=new UserModel();
		$good_model=new GoodsModel();
		$user=$user_model->get($user_id);//买家
		$good=$good_model->get($good_id);//物品
		if(1==$sel)
		{
			$tempStr=$user->purchased;
			$purchase=explode(';',$tempStr);
			$key=array_search($good_id,$purchase);
			if(!$key)
				return json(["status"=>"fail","messege"=>"此商品不在您的已购清单中！"]);
			unset($purchase[$key]);
			$purchase_count=count($purchase);
			if($purchase_count>1)
			for($i=1;$i<$purchase_count;$i++)
			{
				$purchaseinfo[$i-1]=$good_model->field("name,owner,price,id")->find($purchase[$i]);
			}
			else
				$purchaseinfo=null;
			$tempStr=implode(';',$purchase);
			$user->purchased=$tempStr;
			
			
			if(!$user->save())
				return json(["status"=>"fail","messege"=>"信息更新失败请联系网站管理员！"]);
			else{
				return json(["status"=>"ok","info"=>json_encode($purchaseinfo)]);
				}
		}
		else if(2==$sel)
		{
			$tempStr=$user->likes;
			$likes=explode(';',$tempStr);
			$key=array_search($good_id,$likes);
			if(!$key)
				return json(["status"=>"fail","messege"=>"此商品不在您的收藏清单中！"]);
			unset($likes[$key]);
			$good->setDec("likes");
			$likes_count=count($likes);
			if($likes_count>1)
				for($i=1;$i<$likes_count;$i++)
				{
					$likesinfo[$i-1]=$good_model->field("name,owner,price,id")->find($likes[$i]);
				}
			else
				$likesinfo=null;

			$tempStr=implode(';',$likes);
			$user->likes=$tempStr;
			if(!$user->save())
				return json(["status"=>"fail","messege"=>"信息更新失败请联系网站管理员！"]);
			else{
				return json(["status"=>"ok","info"=>json_encode($likesinfo)]);
				}
		}
		else if(0==$sel)
		{	
			if(!$good)
				return json(["status"=>"fail","messege"=>"商品已近下架！"]);
			if(0==$good->delete())
				return json(["status"=>"fail","messege"=>"信息更新失败请联系网站管理员！"]);
			$goodinfo=$good_model->where("owner_id",$user_id)->field("name,expire,price,id")->select();
				return json(["status"=>"ok","info"=>json_encode($likesinfo)]);
		}
	}
	public function updateInfo()
	{
		$user_id=Session::get("id","personinfo");
		if(!$user_id)
			return json(["status"=>"fail","messege"=>"请先登录后再继续操作！"]);
		$user=new UserModel();
		$user=$user->get($user_id);
		$para=Request::instance()->param();
		
		$sel=$para["sel"];
		if(4==$sel)
		{
		//头像处理
		$img=request()->file()['file'];

		//部署时候需要改变位置
		$imgInfo=$img->move(ROOT_PATH . "public\uploads");
		if(false==$imgInfo)
			return json(['status'=>'fail','messege'=>"商品图片存储存在问题，请重试",'url'=>"\\"]);
		$path="\\uploads\\" . $imgInfo->getSaveName();
		$user->headshot=$path;
		
		if($user->save())
		{
			return json(['status'=>'ok']);
		}
		else
			return json(["status"=>"fail"]);
		}
		$text=$para["text"];
		if(0==$sel)
		{
			$user->username=$text;
		}
		else if(1==$sel)
		{
			$user->school=$text;
		}
		else if(2==$sel)
		{
			$user->contact=$text;
		}
		else if(3==$sel)
		{
			$user->email=$text;
		}
		else if(5==$sel)
		{
			$email_token=$user->email_token;
			$url='http://www.funnywtx.com'.url('index/register/checkmail',['id'=>$user_id,'email_token'=>$email_token]);
			$date=date('Y');
			$messege="<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8' /><title>W.T.X</title><meta name='viewport' content='width=device-width, initial-scale=1.0' /></head><body style='padding:0;margin:0'><table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td>
								<table align='center' border='1' cellpadding='0' cellspacing='0' width='600' style='border-collapse: collapse;'>
									<tr>
										<td bgcolor='#000000'>
											<p style ='color:#ffffff;font-size:25px;margin:11px' >W.T.X</p>
										</td>
									</tr>
									<tr style='border:0'>
										<td  bgcolor='#ffffff' style='border:none'>
											<p style='margin:5px;color:#000000;font-size:18px'>尊敬的:KOMO</p>
											<p style='margin-left:50px;margin-top:50px;margin-right:50px;margin-bottom:40px;text-align:center'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;欢迎注册W.T.X账号，请点击一下链接完成注册：<b style='font-size:12px'><br/>（如非本人操作请忽视本条消息）</b></p>
											<p style='text-align:center;margin-top:-20px;margin-bottom:50px' ><a href='{$url}'>".'http://www.funnywtx.com'.	url('index/register/checkmail',['id'=>$user_id,'email_token'=>$email_token])."</a></p>
										</td>
									</tr>
									<tr style='border:0'>
										<td style='border:none'>
											<p style='color:#808080 ;margin:0;margin-bottom:10px; font-size:12px;text-align:center' >------------(系统邮件，请勿回复)-----------</p>
										</td>
									</tr>
									<tr style='border:0'>
										<td style='border:none'>
                            
											<p style='margin:0px;color:#808080;font-size:10px;text-align:center'>我们的邮箱(Email)：mrbeamcn@gmail.com&nbsp;&nbsp;&nbsp;&nbsp;我们的地址(Address):位置不在可测范围</p>
											<p style='color:#808080 ;margin:0;font-size:12px;text-align:center;overflow:hidden'>-------------------------------------------------------------------------------------------------------------------</p>
										</td>
									</tr>
													<tr style='border:0'>
														<td bgcolor='#FFFFFF' style='border:none'>
															<p style='margin-top:0.5px;margin-bottom:4px; text-align:center;font-size:12px'>COPYRIGHT © 2017 – {$date} TENCENT. ALL RIGHTS RESERVED.</p>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</body>
								</html>";
			$mail = new PHPMailer();  
  
            $mail->isSMTP();// 使用SMTP服务  
            $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码  
            $mail->Host = "smtp.mxhichina.com";// 发送方的SMTP服务器地址  
            $mail->SMTPAuth = true;// 是否使用身份验证  
            $mail->Username = "postmaster@funnywtx.xin";// 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱</span><span style="color:#333333;">  
            $mail->Password = "Uestc171013";// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！</span><span style="color:#333333;">  
            $mail->SMTPSecure = "ssl";// 使用ssl协议方式</span><span style="color:#333333;">  
            $mail->Port = 465;// 163邮箱的ssl协议方式端口号是465/994  
  					$mail->IsHTML(true);
            $mail->setFrom("postmaster@funnywtx.xin","W.T.X");// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示  
            $mail->addAddress($user->email);// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)  
            $mail->addReplyTo("mrbeamcn@gmail.com","KOMO");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址  
            //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)  
            //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)  
            //$mail->addAttachment("bug0.jpg");// 添加附件  
  
  
            $mail->Subject = "W.T.X邮箱验证";// 邮件标题  
            $mail->Body = $messege;// 邮件正文  
            //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用  
  
            if($mail->send()){// 发送邮件
                 return  json(["status"=>'ok']);
            }else{  
            		return	json(['status'=>'fail','messege'=>'邮件发送失败请联系网站管理员']);    
            }  
		}
		if($user->save())
		{
			return json(['status'=>'ok']);
		}
		else
			return json(["status"=>"fail"]);
	}
	public function modPassword()
	{
		$para=request()->param();
		$old=$para["old"];
		$new=$para["new"];
		$again=$para["again"];
		$user_id=Session::get("id","personinfo");
		if(!$user_id)
			return json(["status"=>"fail","messege"=>"登录错误！"]);
		if($again!=$new)
			return json(["status"=>"fail","messege"=>"两次输入的密码不同！"]);
		$user_model=new UserModel();
		$user=$user_model->get($user_id);
		if($user->password!=MD5($old))
			return json(["status"=>"fail","messege"=>"原密码不正确！"]);
		else{
			$user->password=MD5($new);
			if($user->save())
				return json(["status"=>"ok"]);
			else
				return json(["status"=>"ok","messege"=>"密码更新失败"]);
		}
	}
	public function getBuyInfo()
	{
		$good_id=request()->param("id");
		$good_model=new GoodsModel();
		$good=$good_model->field("id,owner_id,name,price,owner,likes,expire")->select($good_id)[0];
		if(!$good)
			return json(["status"=>"fail","messege"=>"商品不存在"]);
		$user_model=new UserModel();
		$user=$user_model->field("contact,email")->select($good->owner_id)[0];
		if(!$user)
			return json(["status"=>"fail","messege"=>"用户不存在"]);
		return json(["status"=>"ok","userinfo"=>$user->toJson(),"goodinfo"=>$good->toJson()]);
	}
}

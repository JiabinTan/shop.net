<?php
namespace app\index\controller;
use \think\Controller;
use \think\Request;
use \think\Session;
use \app\index\model\Register as RegModel;
use \app\index\validate\Register as RegVali;
use phpmailer\PHPMailer;
class Register extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
	public function checkUsername()
	{
		$validate=\think\Loader::validate('Register');
		$data=Request::instance()->param();
		if($validate->check($data))
			return 'ok';
		else
			return 'fail';
	}
	public function reg()
	{ 
		$req=Request::instance();
		$validate=\think\Loader::validate('Register');
		$data=$req->param();
		if($data['password']!=$data['password-again'])
			return json(['status'=>'fail','messege'=>'password entered twice is not same!']);
		if($validate->rule([
		'username'=>'require|unique:user',
		'password'=>'require|Uphead',
		'email'=>'email',
		'school'=>'require',])->check($data));
		
		if($validate->check($data))
		{
			
			$time=date("Y-m-d H:i:s",$req->time());
			$ip=$req->ip();
			//地址后期完善
			$wip=";".$ip."'".$time."'" . "浙江杭州";
					
			
			$reg=new RegModel();
			$reg->ip=$wip;
			$reg->username=$data['username'];
			$reg->password=$data['password'];
			$reg->email=$data['email'];
			$reg->school=$data['school'];
			$reg->email_state=0;
			if(''!=$data['contact'])
			{
			$reg->contact=$data['contact'];
			}
			
			$ok=$reg->save();
			if($ok)
				{
					
					$id=$reg->id;
					$email_token=MD5(time()+(int)$id);
					$reg->where("id",$id)->update(["email_token"=>$email_token]);
					Session::set('id',$id,'personinfo');
					Session::set('username',$reg->username,'personinfo');
					
					$url='http://www.funnywtx.com'.url('index/register/checkmail',['id'=>$id,'email_token'=>$email_token]);
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
                            <p style='text-align:center;margin-top:-20px;margin-bottom:50px' ><a href='{$url}'>".'http://www.funnywtx.com'.	url('index/register/checkmail',['id'=>$id,'email_token'=>$email_token])."</a></p>
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
            $mail->addAddress($data['email']);// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)  
            $mail->addReplyTo("mrbeamcn@gmail.com","KOMO");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址  
            //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)  
            //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)  
            //$mail->addAttachment("bug0.jpg");// 添加附件  
  
  
            $mail->Subject = "W.T.X邮箱验证";// 邮件标题  
            $mail->Body = $messege;// 邮件正文  
            //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用  
  
            if($mail->send()){// 发送邮件
                 return  json(["status"=>'ok',"messege"=>$messege]);
            }else{  
            		return	json(['status'=>'fail','messege'=>'opps!mail is failed to send, please get contact with us.']);    
            }  
					
				}
				else
					return	json(['status'=>'fail','messege'=>'opps!some unknown problems happened, please get contact with us.']);
					//return "数据库问题";
		}
		else
			return json(['status'=>'fail','messege'=>'your infomation is not matched to our requirement, please try again!']);
	}

	public function checkMail($id,$email_token)
	{
		$reg=new RegModel();
		$update=$reg->get($id);
		if(($update->email_state==0)&&($update->email_token==$email_token))
			{
				$update->update(['email_state'=>1],['id'=>$id]);
				return $this->fetch('mail_ok');
			}
		else
			return $this->fetch('mail_fail');
		
	}
}

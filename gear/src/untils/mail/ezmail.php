<?php
class EzMail{
	private $mail;

	public function init(){
		// 实例化PHPMailer核心类
		$this->mail = new PHPMailer();
		// 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
		$this->mail->SMTPDebug = EnvSetup::isDev();
		// 使用smtp鉴权方式发送邮件
		$this->mail->isSMTP();
		// smtp需要鉴权 这个必须是true
		$this->mail->SMTPAuth = true;
		// 链接qq域名邮箱的服务器地址
		$this->mail->Host = EzMailConst::MAIL_HOST;
		// 设置使用ssl加密方式登录鉴权
		$this->mail->SMTPSecure = EzMailConst::MAIL_SECTYPE_SSL;
		// 设置ssl连接smtp服务器的远程服务器端口号
		$this->mail->Port = EzMailConst::MAIL_PORT;
		$this->mail->CharSet = 'GBK';
		$this->mail->FromName = EzMailConst::MAIL_NICKNAME;
		// smtp登录的账号 QQ邮箱即可
		$this->mail->Username = EzMailConst::MAIL_USERNAME;
		// smtp登录的密码 使用生成的授权码
		$this->mail->Password = EzMailConst::MAIL_AUTHCODE;
		// 设置发件人邮箱地址 同登录账号
		$this->mail->From = EzMailConst::MAIL_USERNAME;
		// 邮件正文是否为html编码 注意此处是一个方法
		$this->mail->isHTML(true);
				
		return $this;
	}

	// 设置发送的邮件的编码
	public function setChar($charSet = 'GBK'){
		$this->mail->CharSet = $charSet;
		return $this->mail;
	}

	public function setAtt($file){
		if(!file_exists($file)){
			Assert::exception("不存在的文件路径！");
		}
		// 为该邮件添加附件
		$this->mail->addAttachment($file);
		return $this->mail;
	}

	// 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
	public function setFromName($fromName = EzMailConst::MAIL_NICKNAME){
		$this->mail->FromName = $fromName;
		return $this->mail;
	}

	public function sendMail($addAddress, $subject, $body){
		// 设置收件人邮箱地址
		foreach($addAddress as $address){
			$this->mail->addAddress($address);
		}	
		// 添加该邮件的主题
		$this->mail->Subject = $subject;
		// 添加邮件正文
		$this->mail->Body = $body;
		// 发送邮件 返回状态
		return $this->mail->send();
	}

}
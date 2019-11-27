<?php
class Mail{

	public static function sendMail($subject, $content, $addresses = [], $attachs = [], $type = 'hdfguohan'):bool
    {/*{{{*/
        $mailConfig = Config::get('mail')[$type] ?? [];
        if(empty($mailConfig))
        {
           DBC::throwEx('【Mail Exception】wrong config type:'.$type);
        }
        if(empty($addresses))
        {
            DBC::throwEx('【Mail Exception】empty address'); 
        }
		// 实例化PHPMailer核心类
		$mail = new PHPMailer();
		// 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
		$mail->SMTPDebug = 0;
		// 使用smtp鉴权方式发送邮件
		$mail->isSMTP();
		// smtp需要鉴权 这个必须是true
		$mail->SMTPAuth = true;
		// 链接qq域名邮箱的服务器地址
		$mail->Host = $mailConfig['host'];
		// 设置使用ssl加密方式登录鉴权
		$mail->SMTPSecure = 'ssl';
		// 设置ssl连接smtp服务器的远程服务器端口号
		$mail->Port = $mailConfig['port'];
		// 设置发送的邮件的编码
		$mail->CharSet = 'GBK';
		// 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
		$mail->FromName = 'haodf';
		// smtp登录的账号 QQ邮箱即可
		$mail->Username = $mailConfig['userName'];
		// smtp登录的密码 使用生成的授权码
		$mail->Password = $mailConfig['passWord'];
		// 设置发件人邮箱地址 同登录账号
		$mail->From = $mailConfig['from'];
		// 邮件正文是否为html编码 注意此处是一个方法
		$mail->isHTML(true);
		// 设置收件人邮箱地址
        if(!is_array($addresses))
            $addresses = [$addresses];
        foreach($addresses as $address)
        {
		    $mail->addAddress($address);
        }
		// 添加该邮件的主题
		$mail->Subject = $subject;
		// 添加邮件正文
		$mail->Body = $content;
		// 为该邮件添加附件
        if(!is_array($attachs))
            $attachs = [$attachs];
        foreach($attachs as $attach)
        {
            if(!is_file($attach))
            {
                DBC::throwEx('【Mail Exception】wrong path:'.$attach);
                continue; 
            }
		    $mail->addAttachment($attach);
        }
		// 发送邮件 返回状态
		return $mail->send();
	}/*}}}*/

}

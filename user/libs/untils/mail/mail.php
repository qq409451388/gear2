<?php
class Mail{

	public static function sendMail($subject, $content, $addresses = [], $attachs = [], $type = 'hdfguohan'):bool
    {/*{{{*/
        $mailConfig = Config::get('mail')[$type] ?? [];
        if(empty($mailConfig))
        {
           DBC::throwEx('��Mail Exception��wrong config type:'.$type);
        }
        if(empty($addresses))
        {
            DBC::throwEx('��Mail Exception��empty address'); 
        }
		// ʵ����PHPMailer������
		$mail = new PHPMailer();
		// �Ƿ�����smtp��debug���е��� �����������鿪�� ��������ע�͵����� Ĭ�Ϲر�debug����ģʽ
		$mail->SMTPDebug = 0;
		// ʹ��smtp��Ȩ��ʽ�����ʼ�
		$mail->isSMTP();
		// smtp��Ҫ��Ȩ ���������true
		$mail->SMTPAuth = true;
		// ����qq��������ķ�������ַ
		$mail->Host = $mailConfig['host'];
		// ����ʹ��ssl���ܷ�ʽ��¼��Ȩ
		$mail->SMTPSecure = 'ssl';
		// ����ssl����smtp��������Զ�̷������˿ں�
		$mail->Port = $mailConfig['port'];
		// ���÷��͵��ʼ��ı���
		$mail->CharSet = 'GBK';
		// ���÷������ǳ� ��ʾ���ռ����ʼ��ķ����������ַǰ�ķ���������
		$mail->FromName = 'haodf';
		// smtp��¼���˺� QQ���伴��
		$mail->Username = $mailConfig['userName'];
		// smtp��¼������ ʹ�����ɵ���Ȩ��
		$mail->Password = $mailConfig['passWord'];
		// ���÷����������ַ ͬ��¼�˺�
		$mail->From = $mailConfig['from'];
		// �ʼ������Ƿ�Ϊhtml���� ע��˴���һ������
		$mail->isHTML(true);
		// �����ռ��������ַ
        if(!is_array($addresses))
            $addresses = [$addresses];
        foreach($addresses as $address)
        {
		    $mail->addAddress($address);
        }
		// ��Ӹ��ʼ�������
		$mail->Subject = $subject;
		// ����ʼ�����
		$mail->Body = $content;
		// Ϊ���ʼ���Ӹ���
        if(!is_array($attachs))
            $attachs = [$attachs];
        foreach($attachs as $attach)
        {
            if(!is_file($attach))
            {
                DBC::throwEx('��Mail Exception��wrong path:'.$attach);
                continue; 
            }
		    $mail->addAttachment($attach);
        }
		// �����ʼ� ����״̬
		return $mail->send();
	}/*}}}*/

}

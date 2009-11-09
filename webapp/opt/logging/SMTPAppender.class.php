<?php
define("SMTP_APPENDER_XMAILER", "SMTPAppender");
define("SMTP_APPENDER_SUBJECT", "Message from Mojavi");

/**
 * EmailAppender
 * 
 * ��å�������᡼��˽񤯡ʥ᡼��������
 *
 * @author Masaki Komagata <komagata@p0t.jp>
 * @package mojavi
 * @package logging
 * @since 2.0
 */
class SMTPAppender extends Appender {

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param Layout Layout���󥹥���
	 * @param string $to �᡼�밸�襢�ɥ쥹
	 * @param string $subject �᡼���̾
	 * @param string $from �᡼�뺹�пͥ��ɥ쥹
	 * @access public
	 * @since  2.0
	 */
	function &SMTPAppender($layout, $to, $from = null, $subject = SMTP_APPENDER_SUBJECT) {
		parent::Appender($layout);
		$this->to = $to;
		$this->subject = $subject;
		$this->from = $from;
	}

	/**
	 * ��å�������᡼��˽񤯡ʥ᡼��������<br />
	 * 
	 * <note>�褷�Ƽ�ư�ǸƤ�ǤϤ����ʤ�</note>
	 *
	 * @param string �񤫤���å�����
	 * @access public
	 * @since  2.0
	 */
	function write($message) {
		$header = "X-Mailer: ".SMTP_APPENDER_XMAILER."\r\n";
		if ($this->from) { $header .= "From: ".$this->from; }
		if (!mb_send_mail($this->to, $this->subject, $message)) {
			trigger_error("Failed to send mail to ".$this->to, E_USER_ERROR);
		}
	}
}
?>

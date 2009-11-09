<?php
define("SMTP_APPENDER_XMAILER", "SMTPAppender");
define("SMTP_APPENDER_SUBJECT", "Message from Mojavi");

/**
 * EmailAppender
 * 
 * メッセージをメールに書く（メールを送る）
 *
 * @author Masaki Komagata <komagata@p0t.jp>
 * @package mojavi
 * @package logging
 * @since 2.0
 */
class SMTPAppender extends Appender {

	/**
	 * コンストラクタ
	 *
	 * @param Layout Layoutインスタンス
	 * @param string $to メール宛先アドレス
	 * @param string $subject メール件名
	 * @param string $from メール差出人アドレス
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
	 * メッセージをメールに書く（メールを送る）<br />
	 * 
	 * <note>決して手動で呼んではいけない</note>
	 *
	 * @param string 書かれるメッセージ
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

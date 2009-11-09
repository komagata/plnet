<?php
require_once 'Mail.php';
require_once 'Mail/mime.php';

/**
 * PEAR's Mail:: and Mail_mime:: wrapper.
 *
 * @access public
 * @version $Revision: 1.8 $
 * @package MailUtils
 */
class MailUtils
{
    /**
     * Provides an interface for getting Mail::factory parameter by driver
     * types
     *
     * @param string $driver The kind of Mail:: object to instantiate.
     * @param array  $params The parameters to pass to the Mail:: object.
     * @return array $params Parameters depends on $driver
     * @access public
     */
    function getDriverParameters($driver, $params=null)
    {
        switch ($driver) {
            case 'mail':
                break;
            case 'sendmail':
                $params['sendmail_path'] = (!empty($params['sendmail_path'])) ? $params['sendmail_path'] : '/usr/sbin/sendmail';
                //$params['sendmail_args'] = (!empty($params['sendmail_path'])) ? $params['sendmail_path'] : null;
                break;
            case 'smtp':
                $params['host']     = (!empty($params['host'])) ? $params['host'] : 'some.smtpserver.here';
                $params['port']     = (!empty($params['port'])) ? $params['port'] : '25';
                $params['auth']     = true;
                $params['username'] = (!empty($params['username'])) ? $params['username'] : 'user@some.smtpserver.here';
                $params['password'] = (!empty($params['password'])) ? $params['password'] : 'somepassword';
                break;
            default:
                break;
        }
        return $params;
    }

    /**
     * Provides an interface for getting Mail_mime:: parameter by locale
     *
     * @param string $locale The kind of locale.
     * @return array $params Mime parameters depends on $locale
     * @access public
     */
    function getMimeParametersByLocale($locale=null)
    {
            switch ($locale) {
            case 'ja' :
                $params['text_charset']  = 'iso-2022-jp';
                $params['html_charset']  = 'Shift_JIS';
                $params['html_encoding'] = 'base64';
                break;
            default:
                break;
        }
        return $params;
    }

    /**
     * Provides an interface for converting mail subject parameter by locale
     *
     * @param string $subject The subject string.
     * @param string $locale The locale to convert.
     * @return string $locale The converted mail subject.
     * @access public
     */
    function convertSubjectByLocale($subject, $locale=null)
    {
        switch ($locale) {
            case 'ja':
                $subject = mb_encode_mimeheader(mb_convert_encoding($subject, 'JIS', mb_detect_encoding($subject)), 'iso-2022-jp');
                break;
            default:
                break;
        }
        return $subject;
    }

    /**
     * Provides an interface for converting mail body parameter by locale
     *
     * @param string $body The body string.
     * @param string $locale The locale to convert.
     * @return string $body The converted mail body.
     * @access public
     */
    function convertBodyByLocale($body, $locale=null, $type='text')
    {
        switch ($locale) {
            case 'ja':
                $body = mb_convert_encoding($body, ($type=='text') ? 'JIS' : 'SJIS', mb_detect_encoding($body));
                break;
            default:
                break;
        }
        return $body;
    }

    /**
     * Provides an interface for sending a mail
     *
     * @param string $to The e-mail address of recipient.
     * @param string $subject The subject
     * @param string $message The message
     * @param array  $header Mail headers
     * @param array  $params Additional parameters
     * @param string $driver Driver to send mail.
     * @param string $locale Locale to specify the character code.
     * @return mixed Send a mail or if fails a PEAR Error
     * @access public
     */
    function send($to, $subject, $message, $header=array(), $params=array(), $driver='sendmail', $locale='ja')
    {
        $crlf              = "\r\n";
        $subject           = MailUtils::convertSubjectByLocale($subject, $locale);
        $body              = MailUtils::convertBodyByLocale($message, $locale);
        //$htmlBody          = MailUtils::convertBodyByLocale($message, $locale, "html");
        $mimeParams        = MailUtils::getMimeParametersByLocale($locale);
        $driverParams      = MailUtils::getDriverParameters($driver);

        $header['Subject'] = $subject;
        $header['To']      = $to;

        $mime = new Mail_mime($crlf);
        //set area of text
        $mime->setTXTBody($body);
        //set area of html
        //$mime->setHTMLBody($htmlBody);

        //set template file
        foreach ($_FILES as $attach) {
            if (file_exists($attach['tmp_name'])) {
                $mime->addAttachment($attach['tmp_name']
                ,'application/octet-stream'
                ,mb_encode_mimeheader($attach['name'])
                );
            }
        }
        //get mail body part
        $body = $mime->get($mimeParams);
        //get mail header part
        $header = $mime->headers($header);

        $mail =& Mail::factory($driver, $driverParams);
        if (PEAR::isError($mail)) {
            trigger_error('ERROR: ' . $mail->getMessage(), E_USER_WARNING);
            exit;
        }
        $result = $mail->send($to, $header, $body);
        if (PEAR::isError($result)) {
            trigger_error('ERROR: ' . $result->getMessage(), E_USER_WARNING);
            exit;
        }
    }
}
?>

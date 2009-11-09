<?php
require_once BASE_DIR . 'opt/logging/SMTPAppender.class.php';
require_once LOGGING_DIR . 'FileAppender.class.php';

class LogUtils
{
    function initialize()
    {
        $logManager =& LogManager::getInstance();

        // default logger
        $defaultLogger =& LogManager::getLogger();
        $defaultLogger->setPriority(LEVEL_WARN);
        $defaultLogger->setExitPriority(LEVEL_FATAL);
        $layout =& new PatternLayout('%N [%d] %m in %f on line %l%n');
        $fileAppender =& new FileAppender($layout, LOG_DIR . '%d{ymd}.log');
        $defaultLogger->addAppender('file', $fileAppender);
        set_error_handler(array(&$defaultLogger, 'standard'));

        // debug logger
        $debugLogger =& new Logger();
        $debugLogger->setPriority(LEVEL_DEBUG);
        $debugLogger->setExitPriority(LEVEL_FATAL);
        $simpleLayout =& new PatternLayout('%N [%d] %m%n');
        $fileAppender =& new FileAppender($simpleLayout, LOG_DIR . 'debug_%d{ymd}.log');
        $debugLogger->addAppender('file', $fileAppender);
        $stdoutAppender =& new StdoutAppender($simpleLayout);
        $debugLogger->addAppender('stdout', $stdoutAppender);
        $logManager->addLogger('debuglogger', $debugLogger);

        if (MOJAVI_ENV == 'production') {
            $defaultLogger->removeAppender('stdout');
            $debugLogger->removeAppender('stdout');
            $smtpAppender =& new SMTPAppender($layout, PLNET_ERROR_MAIL_TO, 'info@plnet.jp', 'Plnet Error');
            $defaultLogger->addAppender('smtp', $smtpAppender);
        } else if (isset($_SERVER['REQUEST_URI'])) {
            $debugLogger->removeAppender('stdout');
        }
    }

    function debug($message, $file = null, $line = null)
    {
        $logManager =& LogManager::getInstance();
        $logger = $logManager->getLogger('debuglogger');

        $options = array(
            'm' => $message,
            'N' => 'DEBUG',
            'p' => LEVEL_DEBUG
        );

        if ($file) $options['f'] = $file;
        if ($line) $options['l'] = $line;

        $message =& new Message($options);
        $logger->log($message);
    }
}
LogUtils::initialize();
?>

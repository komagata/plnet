<?php
require_once dirname(dirname(__FILE__)) . '/webapp/config.php';
echo shell_exec("mysqldump -u{$db_user} -p{$db_pass} -h{$db_host} {$db_name} | gzip > " . DB_BACKUP_DIR . "attach_`date +%Y%m%d%H%M%S`.dmp.gz");
?>

#!/bin/sh
ssh dev@dev.plnet.jp "mysqldump -uplnet -paruimi plnet --opt > /tmp/plnet.sql"
scp dev@dev.plnet.jp:/tmp/plnet.sql /tmp/plnet.sql
ssh dev@dev.plnet.jp "rm /tmp/plnet.sql"
echo 'SET FOREIGN_KEY_CHECKS = 0;' > /tmp/disable.txt
cat /tmp/disable.txt /tmp/plnet.sql > /tmp/result.sql
mysql -uplnet -paruimi plnet < /tmp/result.sql

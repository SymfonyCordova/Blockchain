<?php

use Phpmig\Migration\Migration;

class Log extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "CREATE TABLE log (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                user_id int(10) unsigned NOT NULL DEFAULT '0',
                module varchar(32) NOT NULL,
                action varchar(32) NOT NULL,
                message text NOT NULL,
                data text,
                ip varchar(255) NOT NULL,
                created_time int(10) unsigned NOT NULL,
                level varchar(10) NOT NULL,
                PRIMARY KEY (id),
                KEY user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统日志';";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    /**
     * Undo the migration
     */
    public function down()
    {

    }
}

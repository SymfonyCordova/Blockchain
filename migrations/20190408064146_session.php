<?php

use Phpmig\Migration\Migration;

class Session extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "CREATE TABLE `sessions` (
            `sess_id` VARBINARY(128) NOT NULL PRIMARY KEY,
            `sess_data` BLOB NOT NULL,
            `sess_time` INTEGER UNSIGNED NOT NULL,
            `sess_lifetime` MEDIUMINT NOT NULL
        )";
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

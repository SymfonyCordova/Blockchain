<?php

use Phpmig\Migration\Migration;

class AddSessionTableUser extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "alter table `user` add login_session_id varchar(255) comment '用户登陆的session_id' ";
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

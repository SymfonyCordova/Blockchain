<?php

use Phpmig\Migration\Migration;

class InitUserconnectionTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "create table UserConnection(
		user_id varchar(255) not null comment '业务系统的user_id',
		provider_id varchar(255) not null comment '服务提供商id，例如QQ，weixin...',
		provider_user_id varchar(255) comment 'openid',
		rank int not null comment '等级',
		display_name varchar(255),
		profile_url varchar(512),
		imgage_url varchar(512),
		accesstoken varchar(512) not null comment '当前用户的令牌',
		secret varchar(512),
		refresh_token varchar(512),
		expire_time bigint,
		primary key (user_id,provider_id,provider_user_id)
	);
	create unique index UserConnectionRank on UserConnection(user_id,provider_id,rank);";
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

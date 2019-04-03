<?php

use Phpmig\Migration\Migration;

class User extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `nickname` varchar(25) NOT NULL COMMENT '昵称',
  `mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号码',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `type` varchar(32) NOT NULL COMMENT '类型:teacher,student',
  `salt` varchar(32) NOT NULL COMMENT '密码SALT',
  `password` varchar(64) NOT NULL COMMENT '用户密码',
  `roles` varchar(255) NOT NULL COMMENT '用户角色',
  `small_avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '小头像',
  `medium_avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '中头像',
  `large_avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '大头像',
  `sex` char(10) NOT NULL DEFAULT '' COMMENT '性别',
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否被禁止',
  `new_notification_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '未读消息数',
  `created_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '注册IP',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `login_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';";
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

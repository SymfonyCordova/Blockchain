<?php

namespace Biz\User\Dao\Impl;

use Biz\User\Dao\UserDao;
use Biz\Common\Dao\BaseDaoImpl;

class UserDaoImpl extends BaseDaoImpl implements UserDao
{
    protected $table = 'user';

    public function declares()
    {
        return array(
            'conditions' => array(
                'username = :username', 
                'nickname = :nickname',
                'mobile = :mobile',
                'email = :email',
                'type = :type',
                'salt = :salt',
                'password = :password',
                'roles = :roles',
                'small_avatar = :small_avatar',
                'medium_avatar = :medium_avatar',
                'large_avatar = :large_avatar',
                'sex = :sex',
                'locked = :locked',
                'new_notification_num = :new_notification_num',
                'created_ip = :created_ip',
                'created_time = :created_time',
                'updated_time = :updated_time',
                'login_time = :login_time',
                'login_ip = :login_ip',

            ),
            'timestamps' => array(
                'created_time', 
                'updated_time',

            ),
            'orderbys' => array(
                'created_time', 
                'updated_time',
            ),
            'serializes' => array(),
        );
    }

    public function findByIds(array $ids)
    {
        return $this->findInField('id', $ids);
    }

    public function getByEmail($email)
    {
        return $this->getByFields(array('email' => $email));
    }

    public function getByMobile($mobile)
    {
        return $this->getByFields(array('mobile' => $mobile));
    }

    public function getByUsername($username)
    {
        return $this->getByFields(array('username'=>$username));
    }


}

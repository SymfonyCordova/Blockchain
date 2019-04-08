<?php

namespace Biz\User\Dao\Impl;

use Biz\User\Dao\UserconnectionDao;
use Biz\Common\Dao\BaseDaoImpl;

class UserconnectionDaoImpl extends BaseDaoImpl implements UserconnectionDao
{
    protected $table = 'userconnection';

    public function declares()
    {
        return array(
            'conditions' => array(
                'user_id = :user_id', 
                'provider_id = :provider_id',
                'provider_user_id = :provider_user_id',
                'rank = :rank',
                'display_name = :display_name',
                'profile_url = :profile_url',
                'imgage_url = :imgage_url',
                'accesstoken = :accesstoken',
                'secret = :secret',
                'refresh_token = :refresh_token',
                'expire_time = :expire_time',
            ),
            'timestamps' => array(
                
            ),
            'orderbys' => array(
                
            ),
            'serializes' => array(),
        );
    }

    public function findByIds(array $ids)
    {
        return $this->findInField('id', $ids);
    }

    public function getByOpenId($openId)
    {
        return $this->getByFields(array('open_id' => $openId));
    }

    public function getByRanks(array $ranks)
    {
        return $this->getByFields(array(
            'user_id'=>$ranks['user_id'],
            'provider_id'=>$ranks['provider_id'],
            'provider_user_id'=>$ranks['provider_user_id'],
        ));
    }

    public function updateByRanks($ranks, $fields)
    {
        return $this->updateByConditions($ranks, $fields);
    }

}

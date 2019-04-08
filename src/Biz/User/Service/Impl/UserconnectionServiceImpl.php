<?php

namespace Biz\User\Service\Impl;

use Biz\BaseService;
use Biz\User\Service\UserconnectionService;
use AppBundle\Common\ArrayToolkit;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\AccessDeniedException;
use Biz\Common\Exception\NotFoundException;

class UserconnectionServiceImpl extends BaseService implements UserconnectionService
{
    public function getByOpenId($openid)
    {
        return $this->getUserconnectionDao()->getByOpenId($openid);
    }


    public function createOrUpdateQqUser($params)
    {
        $userConnection = $this->getUserconnectionDao()->getByOpenId($params['openId']);
        if(empty($userConnection)){
            return $this->createUserconnection($params);
        }

        return $this->updateUserconnection(array(
            'user_id'=>$params['user_id'],
            'provider_id'=>$params['provider_id'],
            'provider_user_id'=>$params['provider_user_id'],
        ), $params);
    }


    public function getUserconnection(array $ranks)
    {
        return $this->getUserconnectionDao()->getByRanks($ranks);
    }

    public function createUserconnection(array $userconnection)
    {
        $userconnection = $this->filterCreateUserconnectionFields($userconnection);

        $userconnection = $this->getUserconnectionDao()->create($userconnection);

        return $userconnection;
    }

    public function updateUserconnection(array $ranks, array $fields)
    {
        $userconnection = $this->getUserconnection($ranks);

        if (empty($userconnection)) {
            throw new NotFoundException(sprintf('Userconnection id#%s not found', json_encode($ranks)));
        }

        $fields = $this->filterUpdateUserconnectionFields($fields);

        $userconnection = $this->getUserconnectionDao()->updateByRanks($ranks, $fields);

        return $userconnection;
    }

    public function findUserconnectionsByIds($ids)
    {
        return ArrayToolkit::index($this->getUserconnectionDao()->findByIds($ids), 'id');
    }

    public function searchUserconnections($conditions, $orderBy, $start, $limit)
    {
        return $this->getUserconnectionDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function countUserconnections($conditions)
    {
        return $this->getUserconnectionDao()->count($conditions);
    }

    protected function filterCreateUserconnectionFields($fields)
    {
        $requiredFields = array(
            'user_id',
            'provider_id',
            'provider_user_id',
            'accesstoken',
            'refresh_token',
            'expire_time',
        );

        if (!ArrayToolkit::requires($fields, $requiredFields)) {
            throw new InvalidArgumentException(sprintf('Missing required fields when creating Userconnection#%s', json_encode($fields)));
        }

        $default = array(
            'rank' => 0,
            'display_name' => $fields['nickname'],
            'profile_url' => '',
            'imgage_url' => $fields['figureurl'],
            'secret' => '',
        );

        $fields = ArrayToolkit::parts($fields, array_merge($requiredFields, array_keys($default)));
        $fields = array_merge($default, $fields);
        $fields['expire_time'] = time()+$fields['expire_time']-10;

        return $fields;
    }

    protected function filterUpdateUserconnectionFields($fields)
    {
        // 只保留允许更新的字段
        $fields = ArrayToolkit::parts($fields, array(
            'provider_user_id',
            'rank',
            'display_name',
            'profile_url',
            'imgage_url',
            'accesstoken',
            'secret',
            'refresh_token',
            'expire_time',
        ));
        $fields['expire_time'] = time() + $fields['expire_time'] - 10;
        return $fields;
    }

    /**
     * @return \Biz\User\Dao\UserconnectionDao
     */
    protected function getUserconnectionDao()
    {
        return $this->createDao('User:UserconnectionDao');
    }
}

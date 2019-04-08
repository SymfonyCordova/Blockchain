<?php

namespace Biz\User\Service;

interface UserconnectionService
{
    public function getUserconnection(array $ranks);
    
    public function createUserconnection(array $userconnection);

    public function updateUserconnection(array $ranks, array $fields);

    public function findUserconnectionsByIds($ids);

    public function searchUserconnections($conditions, $orderBy, $start, $limit);

    public function countUserconnections($conditions);

    /**
     * 创建或更新QqUser
     */
    public function createOrUpdateQqUser($params);

    public function getByOpenId($openid);
}

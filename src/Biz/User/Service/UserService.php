<?php

namespace Biz\User\Service;

interface UserService
{
    public function getUser($id);

    /**
     * 创建用户
     * @param array $user
     * @return mixed
     */
    public function createUser(array $user);

    public function updateUser($id, array $fields);

    public function findUsersByIds($ids);

    public function searchUsers($conditions, $orderBy, $start, $limit);

    public function countUsers($conditions);

    /**
     * 根据登陆关键字获取需要登陆的用户
     * @param $username
     * @return mixed
     */
    public function getUserByLoginField($keyword);
}

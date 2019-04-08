<?php

namespace Biz\User\Dao;

use Biz\Common\Dao\BaseDao;

interface UserconnectionDao extends BaseDao
{
	public function findByIds(array $ids);

    /**
     * 根据openId获取UserConnection
     */
    public function getByOpenId($openId);

    /**
     * 根据联合主键获取Userconnection
     */
    public function getByRanks(array $ranks);

    public function updateByRanks($ranks, $fields);
}

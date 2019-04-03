<?php

namespace Biz\User\Dao;

use Biz\Common\Dao\BaseDao;

interface UserDao extends BaseDao
{
	public function findByIds(array $ids);

    public function getByEmail($keyword);

    public function getByMobile($keyword);

    public function getByUsername($keyword);
}

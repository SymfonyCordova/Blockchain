<?php

namespace Biz\Common\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface AdvancedDao extends GeneralDaoInterface
{
    public function batchDelete(array $conditions);

    public function batchCreate($rows);

    public function batchUpdate($identifies, $updateColumnsList, $identifyColumn = 'id');
}

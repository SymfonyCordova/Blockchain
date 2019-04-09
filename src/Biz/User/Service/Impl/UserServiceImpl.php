<?php
namespace Biz\User\Service\Impl;

use Biz\BaseService;
use Biz\User\Service\UserService;
use AppBundle\Common\ArrayToolkit;
use Biz\Common\Exception\InvalidArgumentException;
use Biz\Common\Exception\NotFoundException;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use AppBundle\Common\SimpleValidator;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserServiceImpl extends BaseService implements UserService
{
    public function rememberLoginSessionId($id, $sessionId)
    {
        $user = $this->getUser($id);

        if(empty($user)){
            throw new NotFoundException(sprintf('User id#%s not found', $id));
        }

        return $this->getUserDao()->update($id, array(
            'login_session_id' => $sessionId
        ));
    }


    public function getUser($id)
    {
        return $this->getUserDao()->get($id);
    }

    public function createUser(array $user)
    {
        $user = $this->filterCreateUserFields($user);

        $user = $this->getUserDao()->create($user);

        $this->dispatchEvent('user.created', $user);

        //$this->getLogService()->info('user', 'create', 'Create User', $user);

        return $user;
    }

    public function updateUser($id, array $fields)
    {
        $user = $this->getUser($id);

        if (empty($user)) {
            throw new NotFoundException(sprintf('User id#%s not found', $id));
        }

        //$this->checkCurrentUserAccess($user);

        $fields = $this->filterUpdateUserFields($fields);

        $user = $this->getUserDao()->update($id, $fields);

        //$this->dispatchEvent('user.updated', $user);

        //$this->getLogService()->info('user', 'update', 'Update User', $user);

        return $user;
    }

    public function findUsersByIds($ids)
    {
        return ArrayToolkit::index($this->getUserDao()->findByIds($ids), 'id');
    }

    public function searchUsers($conditions, $orderBy, $start, $limit)
    {
        return $this->getUserDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function countUsers($conditions)
    {
        return $this->getUserDao()->count($conditions);
    }

    public function getUserByLoginField($keyword)
    {
        if(SimpleValidator::email($keyword)){
            $user = $this->getUserDao()->getByEmail($keyword);
        } elseif (SimpleValidator::mobile($keyword)){
            $user = $this->getUserDao()->getByMobile($keyword);
        } else {
            $user = $this->getUserDao()->getByUsername($keyword);
        }

        if(empty($user)){
            throw new UsernameNotFoundException(sprintf('User keyword#%s not found', $keyword));
        }

        return $user;
    }

    protected function filterCreateUserFields($fields)
    {
        $requiredFields = array(
            'username',
            'password'
        );

        if (!ArrayToolkit::requires($fields, $requiredFields)) {
            throw new InvalidArgumentException(sprintf('Missing required fields when creating User#%s', json_encode($fields)));
        }


        $default = array(
            'nickname' => 'Jack Black',
            'mobile' => '',
            'email' => '',
            'type' => '',
            'salt' => '',
            'roles' => 'ROLE_USER',
            'small_avatar' => '',
            'medium_avatar' => '',
            'large_avatar' => '',
            'sex' => '',
            'locked' => 1,
            'new_notification_num' => 0,
            'created_ip' => $this->getCurrentUser()->getLoginIp(),
            'login_time' => 0,
            'login_ip' => '',
        );

        $fields = ArrayToolkit::parts($fields, array_merge($requiredFields, array_keys($default)));
        $fields = array_merge($default, $fields);
        $fields['salt'] = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $fields['password'] = $this->getPasswordEncoder()->encodePassword($fields['password'], $fields['salt']);

        return $fields;
    }

    protected function filterUpdateUserFields($fields)
    {
        // 只保留允许更新的字段
        $fields = ArrayToolkit::parts($fields, array(
            'username', 
            'nickname',
            'mobile',
            'email',
            'type',
            'salt',
            'password',
            'roles',
            'small_avatar',
            'medium_avatar',
            'large_avatar',
            'sex',
            'locked',
            'new_notification_num',
            'created_ip',
            'login_time',
            'login_ip',

        ));

        return $fields;
    }

    protected function getPasswordEncoder()
    {
        return new MessageDigestPasswordEncoder('sha256');
    }
    /**
     * @return \Biz\User\Dao\UserDao
     */
    protected function getUserDao()
    {
        return $this->createDao('User:UserDao');
    }


}

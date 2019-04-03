<?php

namespace Tests\DS\Service;

use Biz\BaseTestCase;

class UserServiceTest extends BaseTestCase
{
    public function testCreateUser()
    {
        $createdUsers = $this->createUsers();
        $createdUser = $createdUsers[0];

        $users = $this->mockUsers();
        $user = $users[0];

        $this->assertArrayEquals($user, $createdUser, array_keys($user));
    }

    public function testUpdateUser()
    {
        $createdUsers = $this->createUsers();
        $createdUser = $createdUsers[0];

        $fields = array(
            'name' => 'new name for test',
        );

        $updateUser = $this->getUserService()->updateUser($createdUser['id'], $fields);

        $this->assertEquals($fields['name'], $updateUser['name']);
    }

    protected function createUsers()
    {
        $users = $this->mockUsers();

        $createdUsers = array();

        foreach ($users as $key => $user) {
            $createdUsers[$key] = $this->getUserService()->createUser($user);
        }

        return $createdUsers;
    }

    protected function mockUsers()
    {
        $users = array(
            array(
            	'username' => '', 
'nickname' => '', 
'mobile' => '', 
'email' => '', 
'type' => '', 
'salt' => '', 
'password' => '', 
'roles' => '', 
'small_avatar' => '', 
'medium_avatar' => '', 
'large_avatar' => '', 
'sex' => '', 
'locked' => '', 
'new_notification_num' => 0, 
'created_ip' => '', 
'login_time' => 0, 
'login_ip' => '', 

            ),
        );

        return $users;
    }

    /**
     * @return \Biz\DS\Service\UserService
     */
    protected function getUserService()
    {
        return $this->createService('DS:UserService');
    }
}

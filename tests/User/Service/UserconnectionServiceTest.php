<?php

namespace Tests\User\Service;

use Biz\BaseTestCase;

class UserconnectionServiceTest extends BaseTestCase
{
    public function testCreateUserconnection()
    {
        $createdUserconnections = $this->createUserconnections();
        $createdUserconnection = $createdUserconnections[0];

        $userconnections = $this->mockUserconnections();
        $userconnection = $userconnections[0];

        $this->assertArrayEquals($userconnection, $createdUserconnection, array_keys($userconnection));
    }

    public function testUpdateUserconnection()
    {
        $createdUserconnections = $this->createUserconnections();
        $createdUserconnection = $createdUserconnections[0];

        $fields = array(
            'name' => 'new name for test',
        );

        $updateUserconnection = $this->getUserconnectionService()->updateUserconnection($createdUserconnection['id'], $fields);

        $this->assertEquals($fields['name'], $updateUserconnection['name']);
    }

    protected function createUserconnections()
    {
        $userconnections = $this->mockUserconnections();

        $createdUserconnections = array();

        foreach ($userconnections as $key => $userconnection) {
            $createdUserconnections[$key] = $this->getUserconnectionService()->createUserconnection($userconnection);
        }

        return $createdUserconnections;
    }

    protected function mockUserconnections()
    {
        $userconnections = array(
            array(
            	'user_id' => '', 
'provider_id' => '', 
'provider_user_id' => '', 
'rank' => 0, 
'display_name' => '', 
'profile_url' => '', 
'imgage_url' => '', 
'accesstoken' => '', 
'secret' => '', 
'refresh_token' => '', 
'expire_time' => '', 

            ),
        );

        return $userconnections;
    }

    /**
     * @return \Biz\User\Service\UserconnectionService
     */
    protected function getUserconnectionService()
    {
        return $this->createService('User:UserconnectionService');
    }
}

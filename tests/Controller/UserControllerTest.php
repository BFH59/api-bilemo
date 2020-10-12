<?php


namespace App\Tests\Controller;


use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    // should return status 200 as client is logged and has token
    public function testListAction()
    {
        $client = static::createClient();
        $clientRepository = static::$container->get(ClientRepository::class);
        $testUser = $clientRepository->findOneByEmail('orange@orange.fr');

        $client->request('POST', '/api/login_check', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    "username" => $testUser->getUsername(),
                    "password" => 'password',
                ]
            )
        );

        $response = $client->getResponse();
        $token =  json_decode($response->getContent())->token;

        //$client->loginUser($testUser);

        $client->request('GET', '/api/users', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
// should return status 200 as client is logged and has token and userId: 1 belongs to the Client
    public function testShowAction()
    {
        $client = static::createClient();
        $clientRepository = static::$container->get(ClientRepository::class);
        $testUser = $clientRepository->findOneByEmail('orange@orange.fr');
        $client->request('POST', '/api/login_check', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    "username" => $testUser->getUsername(),
                    "password" => 'password',
                ]
            )
        );

        $response = $client->getResponse();
        $token =  json_decode($response->getContent())->token;

        //$client->loginUser($testUser);

        $client->request('GET', '/api/users/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
// should return status 403 as client is logged and has token and userId: 20 does not belong to the Client

    public function testShowActionKO()
    {
        $client = static::createClient();
        $clientRepository = static::$container->get(ClientRepository::class);
        $testUser = $clientRepository->findOneByEmail('orange@orange.fr');
        $client->request('POST', '/api/login_check', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    "username" => $testUser->getUsername(),
                    "password" => 'password',
                ]
            )
        );

        $response = $client->getResponse();
        $token =  json_decode($response->getContent())->token;

        //$client->loginUser($testUser);
// test with a user from Client FREE, should return 403 forbidden status code because logged in client is Orange
        $client->request('GET', '/api/users/20', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    // should return 201 status code
    public function testCreateAction()
    {
        $client = static::createClient();
        $clientRepository = static::$container->get(ClientRepository::class);
        $testUser = $clientRepository->findOneByEmail('orange@orange.fr');
        $client->request('POST', '/api/login_check', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    "username" => $testUser->getUsername(),
                    "password" => 'password',
                ]
            )
        );

        $response = $client->getResponse();
        $token =  json_decode($response->getContent())->token;

        //$client->loginUser($testUser);
//  User creation linked to logged in client ( orange) should return 201
        $client->request('POST', '/api/users', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json'
        ],
        json_encode(
            [
                "name" => 'test',
                "email" => 'test12345@test'.mt_rand(1,1000).'.com',
                "phone" => '0102030405'
            ]
        ));
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }
//should return status 200 after deletion
    public function testDeleteAction()
    {
        $client = static::createClient();
        $clientRepository = static::$container->get(ClientRepository::class);
        $testUser = $clientRepository->findOneByEmail('orange@orange.fr');
        $client->request('POST', '/api/login_check', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    "username" => $testUser->getUsername(),
                    "password" => 'password',
                ]
            )
        );

        $response = $client->getResponse();
        $token =  json_decode($response->getContent())->token;

        //$client->loginUser($testUser);

        $client->request('DELETE', '/api/users/99', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json'
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    //should return status 403 because user do'esnt belong to the logged in client
    public function testDeleteActionKO()
    {
        $client = static::createClient();
        $clientRepository = static::$container->get(ClientRepository::class);
        $testUser = $clientRepository->findOneByEmail('orange@orange.fr');
        $client->request('POST', '/api/login_check', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    "username" => $testUser->getUsername(),
                    "password" => 'password',
                ]
            )
        );

        $response = $client->getResponse();
        $token =  json_decode($response->getContent())->token;

        //$client->loginUser($testUser);

        $client->request('DELETE', '/api/users/68', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json'
        ]);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
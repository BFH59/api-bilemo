<?php


namespace App\Tests\Controller;


use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PhoneControllerTest extends WebTestCase
{
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

        $client->loginUser($testUser);

        $client->request('GET', '/api/phones', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

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

        $client->loginUser($testUser);

        $client->request('GET', '/api/phones/127', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

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

        $client->loginUser($testUser);
// test with a phone ID that doesn't exist, should return 404 status code
        $client->request('GET', '/api/phones/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
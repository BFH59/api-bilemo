<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     * @param $url
     * @param $expectedStatus
     */
    public function testPageIsSuccessful($url, $expectedStatus)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame($expectedStatus);
    }

    public function urlProvider()
    {
        return [
        ['/api/phones', 401],
        ['/api/phones/127', 401],
        ['/api/users', 401],
        ['/api/users/1', 401],
        ['/api/doc', 200],
        ];
    }
}
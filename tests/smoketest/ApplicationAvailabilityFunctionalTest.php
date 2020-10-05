<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     * @param $url
     */


    public function testPageIsSuccessful($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public function urlProvider()
    {
        return [
        ['/api/phones'],
        ['/api/phones/127'],
        ['/api/users'],
        ['/api/users/1'],
        ];
    }
}
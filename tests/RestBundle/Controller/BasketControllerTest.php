<?php

namespace Tests\RestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasketControllerTest extends WebTestCase
{
    public function testCget()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/baskets');
        $this->assertJsonResponse($client->getResponse(), 200);

        $crawler = $client->request('GET', '/baskets.json');
        $this->assertJsonResponse($client->getResponse(), 200);

    }

    public function testGet()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/baskets/1');
        $this->assertJsonResponse($client->getResponse(), 200);

        $crawler = $client->request('GET', '/baskets/1.json');
        $this->assertJsonResponse($client->getResponse(), 200);

        $crawler = $client->request('GET', '/baskets/0.json');
        $this->assertJsonResponse($client->getResponse(), 404);

    }

    public function testPost()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'POST',
            'http://rest-demo/app_dev.php/baskets',
            [],
            [],
            ['content-type'=>'application/json'],
            '{
                "userId": 3,
                "uuid": "154605ff-5a25-4334-85ae-e68fee44bbbf"
            }'
        );
        $this->assertJsonResponse($client->getResponse(), 200);
    }


    protected function assertJsonResponse($response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode,
            $response->getStatusCode()
        );

        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }

    protected function assertHtmlResponse($response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode,
            $response->getStatusCode()
        );

        $this->assertTrue(
            $response->headers->contains('Content-Type', 'text/html; charset=UTF-8'),
            $response->headers
        );
    }
}

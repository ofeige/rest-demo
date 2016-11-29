<?php

namespace Tests\RestBundle\Controller;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BasketControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->content = array();
    }

    public function testPost()
    {
        $client = static::createClient([], ['HTTP_HOST' => 'rest-demo:8080']);
        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->setServerParameter('HTTP_accept', 'application/json');

        $userId = rand(1,99);
        $uuid = Uuid::uuid4();

        $client->request(
            'POST',
            '/baskets',
            [],
            [],
            [],
            '{"userId": '.$userId.',"uuid": "'.$uuid->toString().'"}'
        );

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 201);
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('userId', $content);
        $this->assertEquals($content['userId'], $userId);
        $this->assertArrayHasKey('uuid', $content);
        $this->assertEquals($content['uuid'], $uuid);
        $this->assertArrayHasKey('basketItems', $content);

        // save information for delete test
        return $content;
    }

    /**
     * @depends testPost
     * @param array $content
     * @return array|mixed
     */
    public function testPut(array $content)
    {
        $client = static::createClient([], ['HTTP_HOST' => 'rest-demo:8080']);
        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->setServerParameter('HTTP_accept', 'application/json');

        $userId = rand(1,99);
        $client->request(
            'PUT',
            '/baskets/'.$content['id'],
            [],
            [],
            [],
            '{"userId": '.$userId.'}'
        );

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 202);
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('userId', $content);
        $this->assertEquals($content['userId'], $userId);
        $this->assertArrayHasKey('uuid', $content);
        $this->assertArrayHasKey('basketItems', $content);

        return $content;
    }

    public function testCget()
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('GET', '/baskets');
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertGreaterThanOrEqual(2, count($content));
        $this->assertArrayHasKey('id', $content[0]);
        $this->assertArrayHasKey('userId', $content[0]);
        $this->assertArrayHasKey('uuid', $content[0]);
        $this->assertArrayHasKey('basketItems', $content[0]);

        $client->request('GET', '/baskets.json');
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertGreaterThanOrEqual(2, count($content));
        $this->assertArrayHasKey('id', $content[0]);
        $this->assertArrayHasKey('userId', $content[0]);
        $this->assertArrayHasKey('uuid', $content[0]);
        $this->assertArrayHasKey('basketItems', $content[0]);

    }

    public function testGet()
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('GET', '/baskets/1');
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertGreaterThanOrEqual(2, count($content));
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('userId', $content);
        $this->assertArrayHasKey('uuid', $content);
        $this->assertArrayHasKey('basketItems', $content);

        $client->request('GET', '/baskets/1.json');
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertGreaterThanOrEqual(2, count($content));
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('userId', $content);
        $this->assertArrayHasKey('uuid', $content);
        $this->assertArrayHasKey('basketItems', $content);

        $client->request('GET', '/baskets/0.json');
        $this->assertJsonResponse($client->getResponse(), 404);

    }

    /**
     * @depends testPut
     * @param $content
     */
    public function testDelete(array $content)
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('DELETE', '/baskets/'.$content['id']);
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 204);

        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('GET', '/baskets/'.$content['id']);
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 404);

    }


    protected function assertJsonResponse(Response $response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode,
            $response->getStatusCode(),
            'http status code is wrong. Got: '.$response->getStatusCode().' expected: '.$statusCode
        );

        // 204 = no content = no need for test content-type
        // 404 = no found = no need for test content-type
        if($statusCode == 204 or $statusCode == 404) {
            return;
        }

        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }

    protected function assertHtmlResponse(Response $response, $statusCode = 200)
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
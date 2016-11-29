<?php

namespace Tests\RestBundle\Controller;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BasketItemControllerTest extends WebTestCase
{

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
        $basket = json_decode($client->getResponse()->getContent(), true);

        $client = static::createClient([], ['HTTP_HOST' => 'rest-demo:8080']);
        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->setServerParameter('HTTP_accept', 'application/json');

        $userId = rand(1,99);
        $uuid = Uuid::uuid4();

        $client->request(
            'POST',
            '/baskets/'.$basket['id'].'/items',
            [],
            [],
            [],
            '{"info":{"product_id":12345,"titel":"Kern Tischwaage"}}'
        );

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 201);
        $this->assertArrayHasKey('id', $content[0]);
        $this->assertArrayHasKey('info', $content[0]);

        // save information for delete test
        return [$basket['id'], $content];
    }

//    /**
//     * @depends testPost
//     * @param array $content
//     * @return array|mixed
//     */
//    public function testPut(array $data)
//    {
//        $basketId = $data[0];
//        $basketItem = $data[1];
//
//        $client = static::createClient([], ['HTTP_HOST' => 'rest-demo:8080']);
//        $client->setServerParameter('CONTENT_TYPE', 'application/json');
//        $client->setServerParameter('HTTP_accept', 'application/json');
//
//        $info = ['product_id'=>rand(10000,99999), 'title'=>'BMW X6'];
//        $client->request(
//            'PUT',
//            sprintf('/baskets/%s/items/%s', $basketId, $basketItem[0]['id']),
//            [],
//            [],
//            [],
//            $info
//        );
//        print_r($client->getRequest()->getBaseUrl());
//
//        $content = json_decode($client->getResponse()->getContent(), true);
//
//        //print_r($content);
//        $this->assertJsonResponse($client->getResponse(), 202);
//        $this->assertArrayHasKey('id', $content);
//        $this->assertArrayHasKey('userId', $content);
//        $this->assertEquals($content['userId'], $userId);
//        $this->assertArrayHasKey('uuid', $content);
//        $this->assertArrayHasKey('basketItems', $content);
//
//        return $content;
//    }

    /**
     * @depends testPost
     * @return mixed
     */
    public function testCget(array $info)
    {
        $basketId = $info[0];
        $basketItem = $info[1];

        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('GET', sprintf('/baskets/%s/items',
            $basketId
        ));
        $content = json_decode($client->getResponse()->getContent(), true);
        $content = $content[0];

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertEquals(2, count($content));
        $this->assertArrayHasKey('id', $content);
        $this->assertEquals($basketItem[0]['id'], $content['id']);
        $this->assertArrayHasKey('info', $content);

        $client->request('GET', sprintf('/baskets/%s/items.json',
            $basketId
        ));
        $content = json_decode($client->getResponse()->getContent(), true);
        $content = $content[0];

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertEquals(2, count($content));
        $this->assertArrayHasKey('id', $content);
        $this->assertEquals($basketItem[0]['id'], $content['id']);
        $this->assertArrayHasKey('info', $content);

        $client->request('GET', '/baskets/0.json');
        $this->assertJsonResponse($client->getResponse(), 404);

        return $info;
    }

    /**
     * @depends testCget
     */
    public function testGet($info)
    {
        $basketId = $info[0];
        $basketItem = $info[1];

        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('GET', sprintf('/baskets/%s/items/%s',
            $basketId,
            $basketItem[0]['id']
        ));
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertEquals(2, count($content));
        $this->assertArrayHasKey('id', $content);
        $this->assertEquals($basketItem[0]['id'], $content['id']);
        $this->assertArrayHasKey('info', $content);

        $client->request('GET', sprintf('/baskets/%s/items/%s.json',
            $basketId,
            $basketItem[0]['id']
            ));
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertEquals(2, count($content));
        $this->assertArrayHasKey('id', $content);
        $this->assertEquals($basketItem[0]['id'], $content['id']);
        $this->assertArrayHasKey('info', $content);

        $client->request('GET', '/baskets/0.json');
        $this->assertJsonResponse($client->getResponse(), 404);

        return $info;
    }

    /**
     * @depends testGet
     * @param $content
     */
    public function testDelete(array $info)
    {
        $basketId = $info[0];
        $basketItem = $info[1];

        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('DELETE', sprintf('/baskets/%s/items/%s',
            $basketId,
            $basketItem[0]['id']
        ));
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 204);

        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('GET', sprintf('/baskets/%s/items/%s',
            $basketId,
            $basketItem[0]['id']
        ));
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

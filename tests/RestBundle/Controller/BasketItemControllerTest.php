<?php

namespace Tests\RestBundle\Controller;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BasketItemControllerTest extends WebTestCase
{

    public function testPost()
    {
        $userId = rand(1,99);
        $uuid = Uuid::uuid4();

        $client = $this->createBasket($userId, $uuid);
        $basket = json_decode($client->getResponse()->getContent(), true);

        // add basketItem to the new Basket
        $newItem1 = ['id'=>rand(10000,99999), 'title'=>'Eine Tüte Luft'];
        $client = $this->createBasketItem($basket['id'], $newItem1);

        $basketItem = json_decode($client->getResponse()->getContent(), true);
        $this->assertJsonResponse($client->getResponse(), 201);
        $this->assertArrayHasKey('id', $basketItem[0]);
        $this->assertArrayHasKey('info', $basketItem[0]);
        $this->assertEquals($basketItem[0]['info'], $newItem1);

        // add a second basketItem to the new Basket
        $newItem2 = ['id'=>rand(10000,99999), 'title'=>'Eine Flasche Wasser'];
        $client = $this->createBasketItem($basket['id'], $newItem2);

        $basketItem = json_decode($client->getResponse()->getContent(), true);
        $this->assertJsonResponse($client->getResponse(), 201);
        $this->assertEquals(2, count($basketItem));
        $this->assertArrayHasKey('id', $basketItem[1]);
        $this->assertArrayHasKey('info', $basketItem[1]);
        $this->assertEquals($basketItem[1]['info'], $newItem2);

        return [$basket['id'], $basketItem];
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
        $originalBasketItem = $info[1];

        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('GET', sprintf('/baskets/%d/items',
            $basketId
        ));
        $basketItems = json_decode($client->getResponse()->getContent(), true);
        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertEquals($basketItems, $originalBasketItem);

        $client->request('GET', sprintf('/baskets/%s/items.json',
            $basketId
        ));
        $basketItems = json_decode($client->getResponse()->getContent(), true);
        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertEquals($basketItems, $originalBasketItem);

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
        $originalBasketItem = $info[1];

        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        // test basketItem 1
        $client->request('GET', sprintf('/baskets/%s/items/%s',
            $basketId,
            $originalBasketItem[0]['id']
        ));
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertEquals($content, $originalBasketItem[0]);

        $client->request('GET', sprintf('/baskets/%s/items/%s.json',
            $basketId,
            $originalBasketItem[0]['id']
            ));
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertEquals($content, $originalBasketItem[0]);

        // test basketItem 2
        $client->request('GET', sprintf('/baskets/%s/items/%s',
            $basketId,
            $originalBasketItem[1]['id']
        ));
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertEquals($content, $originalBasketItem[1]);

        $client->request('GET', sprintf('/baskets/%s/items/%s.json',
            $basketId,
            $originalBasketItem[1]['id']
        ));
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertEquals($content, $originalBasketItem[1]);


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

        // test that the deleted basketItem is not accessible anymore
        $client->request('GET', sprintf('/baskets/%s/items/%s',
            $basketId,
            $basketItem[0]['id']
        ));
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertJsonResponse($client->getResponse(), 404);

        // test that cget is also getting only one basketItem
        $client->request('GET', sprintf('/baskets/%d/items',
            $basketId
        ));
        $basketItems = json_decode($client->getResponse()->getContent(), true);
        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertCount(1, $basketItems);
        $this->assertEquals($basketItems, $basketItem);

        print_r($basketItems);

        return $info;
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

    protected function createBasket($userId, Uuid $uuid)
    {
        $client = static::createClient([], ['HTTP_HOST' => 'rest-demo:8080']);
        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->setServerParameter('HTTP_accept', 'application/json');

        $client->request(
            'POST',
            '/baskets',
            [],
            [],
            [],
            '{"userId": '.$userId.',"uuid": "'.$uuid->toString().'"}'
        );

        return $client;
    }

    protected function createBasketItem($basketId, $content)
    {
        $client = static::createClient([], ['HTTP_HOST' => 'rest-demo:8080']);
        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->setServerParameter('HTTP_accept', 'application/json');

        $client->request(
            'POST',
            '/baskets/'.$basketId.'/items',
            [],
            [],
            [],
            '{"info":'.json_encode($content).'}'
        );
        $client->getResponse()->getContent();

        return $client;
    }
}

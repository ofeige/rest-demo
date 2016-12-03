<?php

namespace Tests\RestBundle\Controller;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BasketControllerTest extends WebTestCase
{
     public function testPost()
    {
        $userId = rand(1,99);
        $uuid = Uuid::uuid4();

        $client = $this->createBasket($userId, $uuid);
        $basket = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 201);
        $this->assertArrayHasKey('id', $basket);
        $this->assertArrayHasKey('userId', $basket);
        $this->assertEquals($basket['userId'], $userId);
        $this->assertArrayHasKey('uuid', $basket);
        $this->assertEquals($basket['uuid'], $uuid);
        $this->assertArrayHasKey('basketItems', $basket);

        // save information for delete test
        return $basket;
    }

    /**
     * @depends testPost
     *
     * @param array $content
     * @return array|mixed
     */
    public function testPut(array $basket)
    {
        $client = static::createClient([], ['HTTP_HOST' => 'rest-demo:8080']);
        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->setServerParameter('HTTP_accept', 'application/json');

        $userId = rand(1,99);
        $client->request(
            'PUT',
            '/baskets/'.$basket['id'],
            [],
            [],
            [],
            '{"userId": '.$userId.'}'
        );

        $updatedBasket = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 202);
        $this->assertArrayHasKey('id', $updatedBasket);
        $this->assertEquals($updatedBasket['id'], $basket['id']);
        $this->assertArrayHasKey('userId', $updatedBasket);
        $this->assertEquals($updatedBasket['userId'], $userId);
        $this->assertArrayHasKey('uuid', $updatedBasket);
        $this->assertEquals($updatedBasket['uuid'], $basket['uuid']);
        $this->assertArrayHasKey('basketItems', $updatedBasket);

        return $updatedBasket;
    }

    /**
     * @depends testPut
     */
    public function testGet(array $basket)
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('GET', sprintf('/baskets/%s', $basket['id']));
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertGreaterThanOrEqual(2, count($content));
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('userId', $content);
        $this->assertArrayHasKey('uuid', $content);
        $this->assertArrayHasKey('basketItems', $content);

        $client->request('GET', sprintf('/baskets/%s.json', $basket['id']));
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertGreaterThanOrEqual(2, count($content));
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('userId', $content);
        $this->assertArrayHasKey('uuid', $content);
        $this->assertArrayHasKey('basketItems', $content);

        $client->request('GET', '/baskets/0.json');
        $this->assertJsonResponse($client->getResponse(), 404);

        return $basket;
    }

    /**
     * @depends testGet
     * @param array $data
     * @return array
     */
    public function testMerge(array $oldBasket)
    {
        $userId = rand(1,99);
        $uuid = Uuid::uuid4();

        // create a new basket
        $client = $this->createBasket($userId, $uuid);
        $basketToMerge = json_decode($client->getResponse()->getContent(), true);

        // add basketItem to the new basket
        $id = rand(10000,99999);
        $client = $this->createBasketItem($basketToMerge['id'], ['id'=>$id, 'title'=>'Eine Tüte Luft']);

        // merge the new Basket to the old one
        $client->request('PATCH', sprintf('/baskets/%s/merge?mergeToBasketId=%s',
            $basketToMerge['id'],
            $oldBasket['id']
        ));
        $basket = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 202);
        $this->assertArrayHasKey('id', $basket);
        $this->assertEquals($basket['id'], $oldBasket['id']);
        $this->assertArrayHasKey('userId', $basket);
        $this->assertEquals($basket['userId'], $oldBasket['userId']);
        $this->assertArrayHasKey('uuid', $basket);
        $this->assertEquals($basket['uuid'], $oldBasket['uuid']);
        $this->assertArrayHasKey('basketItems', $basket);
        $this->assertEquals(count($basket['basketItems']), 1);
        $this->assertEquals($basket['basketItems'][0]['info']['id'], $id);

        return $basket;
    }

    /**
     * @depends testMerge
     */
    public function testCget(array $basket)
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('GET', '/baskets');
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertGreaterThanOrEqual(1, count($content));
        $this->assertArrayHasKey('id', $content[0]);
        $this->assertArrayHasKey('userId', $content[0]);
        $this->assertArrayHasKey('uuid', $content[0]);
        $this->assertArrayHasKey('basketItems', $content[0]);

        $client->request('GET', '/baskets.json');
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertJsonResponse($client->getResponse(), 200);
        $this->assertGreaterThanOrEqual(1, count($content));
        $this->assertArrayHasKey('id', $content[0]);
        $this->assertArrayHasKey('userId', $content[0]);
        $this->assertArrayHasKey('uuid', $content[0]);
        $this->assertArrayHasKey('basketItems', $content[0]);

        return $basket;
    }

    /**
     * @depends testCget
     * @param $content
     */
    public function testDelete(array $basket)
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');

        $client->request('DELETE', '/baskets/'.$basket['id']);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertJsonResponse($client->getResponse(), 204);

        // test that basket is really deleted
        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');
        $client->request('GET', '/baskets/'.$basket['id']);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertJsonResponse($client->getResponse(), 404);

        // test that basketItem is deleted too
        $client = static::createClient();
        $client->setServerParameter('HTTP_HOST', 'rest-demo:8080');
        $client->request('GET', sprintf('/baskets/%d/items/%d', $basket['id'], $basket['basketItems'][0]['id']));
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

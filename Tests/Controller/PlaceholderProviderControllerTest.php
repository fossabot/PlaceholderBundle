<?php

namespace BernhardWebstudio\PlaceholderBundle\Tests\Controller;

use PlaceholderGeneratorServiceTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use BernhardWebstudio\PlaceholderBundle\Tests\AppKernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use BernhardWebstudio\PlaceholderBundle\Tests\PlaceholderTest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use BernhardWebstudio\PlaceholderBundle\DependencyInjection\BernhardWebstudioPlaceholderExtension;

class PlaceholderProviderControllerTest extends WebTestCase
{
    /**
     * @var BernhardWebstudioPlaceholderExtension
     */
    protected $extension;
    /**
     * @var ContainerBuilder
     */
    protected $localContainer;
    /**
     * @var TestClient
     */
    protected $client;

    /**
     *
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->client = static::createClient();
        $this->localContainer = $this->client->getContainer();
    }

    /**
     * Test that a 404 is thrown when requesting a non-existent image
     */
    public function testPlaceholderUnavailableAction()
    {
        // $this->expectException(NotFoundHttpException::class);
        try {
            $this->client->request('GET', '/non-exisitiniging.jpg/placeholder');
            $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            $this->assertInstanceOf(NotFoundHttpException::class, $e);
        }
    }

    /**
     * Test that a valid response is returned upon requesting an exisiting image
     */
    public function testPlaceholderAvailableAction()
    {
        $this->client->request('GET', PlaceholderTest::TEST_IMAGE_INPUT . "/placeholder");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue(\file_exists(PlaceholderTest::TEST_IMAGE_OUTPUT . '.svg'));
        unlink(PlaceholderTest::TEST_IMAGE_OUTPUT . '.svg');
    }
}

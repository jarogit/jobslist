<?php

namespace App\Tests\Service;

use App\Kernel;
use App\Service\RecruitisApi;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class RecruitisApiTest extends KernelTestCase
{
    private const PAGE = 1;
    private const ITEMS_PER_PAGE = 10;
    private const TOTAL = 3;

    /** @var RecruitisApi */
    private $api;
    private $httpResponseMock;
    private $cache;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        // Bind a mock for Guzzle requests
        $this->httpResponseMock = new MockHandler();
        Kernel::getGuzzleHandlerStack()->setHandler($this->httpResponseMock);

        $this->api = $container->get(RecruitisApi::class);
        $this->cache = new FilesystemAdapter();
    }

    public function testGetJobsUnauthorized()
    {
        $this->httpResponseMock->append(new Response(401));
        $this->cache->delete($this->api->makeCacheKey(self::PAGE, self::ITEMS_PER_PAGE));
        $dtoJobs = $this->api->getJobs(self::PAGE, self::ITEMS_PER_PAGE);

        $this->assertFalse($dtoJobs->isOk);
        $this->assertEquals(0, $dtoJobs->total);
    }

    public function testGetJobsConnectionError()
    {
        $this->httpResponseMock->append(
            new RequestException('Connection error', new Request('GET', 'test'))
        );
        $dtoJobs = $this->api->getJobs(self::PAGE, self::ITEMS_PER_PAGE);

        $this->assertFalse($dtoJobs->isOk);
        $this->assertEquals(0, $dtoJobs->total);
    }

    public function testGetJobsSuccess()
    {
        $this->httpResponseMock->append(
            new Response(200, [], json_encode([
                'meta' => ['code' => 'api.found', 'entries_total' => self::TOTAL],
                'payload' => []
            ]))
        );
        $this->cache->delete($this->api->makeCacheKey(self::PAGE, self::ITEMS_PER_PAGE));
        $dtoJobs = $this->api->getJobs(self::PAGE, self::ITEMS_PER_PAGE);

        $this->assertTrue($dtoJobs->isOk);
        $this->assertEquals(self::TOTAL, $dtoJobs->total);
    }

    public function testGetJobsCached()
    {
        // Prepare response just for case when caching doesn't work
        $this->httpResponseMock->append(new Response(500));

        $dtoJobs = $this->api->getJobs(self::PAGE, self::ITEMS_PER_PAGE);

        $this->assertTrue($dtoJobs->isOk);
        $this->assertEquals(self::TOTAL, $dtoJobs->total);
    }
}

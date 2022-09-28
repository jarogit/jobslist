<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Tests\Adapter;

use Predis\Connection\StreamConnection;
use Symfony\Component\Cache\Adapter\RedisAdapter;

/**
 * @group integration
 */
class PredisAdapterTest extends AbstractRedisAdapterTest
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$redis = new \Predis\Client(array_combine(['host', 'port'], explode(':', getenv('REDIS_HOST')) + [1 => 6379]), ['prefix' => 'prefix_']);
    }

    public function testCreateConnection()
    {
        $redisHost = getenv('REDIS_HOST');

        $redis = RedisAdapter::createConnection('redis://'.$redisHost.'/1', ['class' => \Predis\Client::class, 'timeout' => 3]);
        $this->assertInstanceOf(\Predis\Client::class, $redis);

        $connection = $redis->getConnection();
        $this->assertInstanceOf(StreamConnection::class, $connection);

        $redisHost = explode(':', $redisHost);
        $params = [
            'scheme' => 'tcp',
            'host' => $redisHost[0],
            'port' => (int) ($redisHost[1] ?? 6379),
            'persistent' => 0,
            'timeout' => 3,
            'read_write_timeout' => 0,
            'tcp_nodelay' => true,
            'database' => '1',
        ];
        $this->assertSame($params, $connection->getParameters()->toArray());
    }
}

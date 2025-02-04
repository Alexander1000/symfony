<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Tests\Simple;

use Symfony\Bridge\PhpUnit\ForwardCompatTestTrait;

/**
 * @group legacy
 */
class RedisClusterCacheTest extends AbstractRedisCacheTest
{
    use ForwardCompatTestTrait;

    private static function doSetUpBeforeClass()
    {
        if (!class_exists('RedisCluster')) {
            self::markTestSkipped('The RedisCluster class is required.');
        }
        if (!$hosts = getenv('REDIS_CLUSTER_HOSTS')) {
            self::markTestSkipped('REDIS_CLUSTER_HOSTS env var is not defined.');
        }

        self::$redis = new \RedisCluster(null, explode(' ', $hosts));
    }
}

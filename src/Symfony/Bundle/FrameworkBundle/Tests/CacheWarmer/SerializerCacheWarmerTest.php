<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Tests\CacheWarmer;

use Symfony\Bridge\PhpUnit\ForwardCompatTestTrait;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\SerializerCacheWarmer;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use Symfony\Component\Serializer\Mapping\Factory\CacheClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\XmlFileLoader;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;

class SerializerCacheWarmerTest extends TestCase
{
    use ForwardCompatTestTrait;

    public function testWarmUp()
    {
        if (!class_exists(CacheClassMetadataFactory::class) || !method_exists(XmlFileLoader::class, 'getMappedClasses') || !method_exists(YamlFileLoader::class, 'getMappedClasses')) {
            $this->markTestSkipped('The Serializer default cache warmer has been introduced in the Serializer Component version 3.2.');
        }

        $loaders = [
            new XmlFileLoader(__DIR__.'/../Fixtures/Serialization/Resources/person.xml'),
            new YamlFileLoader(__DIR__.'/../Fixtures/Serialization/Resources/author.yml'),
        ];

        $file = sys_get_temp_dir().'/cache-serializer.php';
        @unlink($file);

        $warmer = new SerializerCacheWarmer($loaders, $file);
        $warmer->warmUp(\dirname($file));

        $this->assertFileExists($file);

        $arrayPool = new PhpArrayAdapter($file, new NullAdapter());

        $this->assertTrue($arrayPool->getItem('Symfony_Bundle_FrameworkBundle_Tests_Fixtures_Serialization_Person')->isHit());
        $this->assertTrue($arrayPool->getItem('Symfony_Bundle_FrameworkBundle_Tests_Fixtures_Serialization_Author')->isHit());
    }

    public function testWarmUpWithoutLoader()
    {
        if (!class_exists(CacheClassMetadataFactory::class) || !method_exists(XmlFileLoader::class, 'getMappedClasses') || !method_exists(YamlFileLoader::class, 'getMappedClasses')) {
            $this->markTestSkipped('The Serializer default cache warmer has been introduced in the Serializer Component version 3.2.');
        }

        $file = sys_get_temp_dir().'/cache-serializer-without-loader.php';
        @unlink($file);

        $warmer = new SerializerCacheWarmer([], $file);
        $warmer->warmUp(\dirname($file));

        $this->assertFileExists($file);
    }
}

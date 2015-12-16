<?php

namespace Rareloop\Primer\Tests;

use Rareloop\Primer\FileSystem;

class FileSystemTest extends \PHPUnit_Framework_TestCase
{
    protected $primer;

    /**
     * Bootstrap the system
     */
    public function setup()
    {
        $this->primer = \Rareloop\Primer\Primer::start(array(
            'basePath' => __DIR__.'/primer-test',
        ));
    }

    /**
     * @dataProvider loadingDataProvider
     */
    public function testLoadingData($id, $resolveAlias, $expected)
    {
        $viewData = FileSystem::getDataForPattern($id, $resolveAlias);
        $this->assertEquals($expected, $viewData->toArray());
    }

    public function loadingDataProvider()
    {
        return [
            ['components/filesystem/loading-data', false, ["key1" => "value1", "key2" => "value2"]],
            [' components/filesystem/loading-data', false, ["key1" => "value1", "key2" => "value2"]],
            ['components/filesystem/loading-data ', false, ["key1" => "value1", "key2" => "value2"]],
            ['components/filesystem/loading-data~alias', false, ["key2" => "value2", "key3" => "value3"]],
            ['components/filesystem/loading-data~alias', true, ["key1" => "value1", "key2" => "value2", "key3" => "value3"]],
        ];
    }
}

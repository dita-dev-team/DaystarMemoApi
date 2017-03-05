<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;

class AssetTest extends TestCase
{
    use DatabaseTransactions;

    public function testAssetCreation()
    {
        $asset = factory(App\Asset::class)->create();

        $assets = App\Asset::all();
        $this->assertCount(1, $assets, 'Count should be equal to 1');
        $this->assertEquals($asset->description, $assets->first()->description);
        $this->assertEquals($asset->name, $assets->first()->name);
    }

    public function testAssetRoute()
    {
        $description = 'just a random image';
        $path = storage_path('testing/image.jpg');
        $name = 'image.jpg';
        $mimeType = 'image/jpeg';
        $size = 185134;
        $error = null;
        $test = true;
        $file = new UploadedFile($path, $name, $mimeType, $size, $error, $test);
        $this->call('POST', '/api/assets', [
            'description' => $description,
        ], [], ['asset' => $file], []);

        $this->assertResponseOk();
        $assets = App\Asset::all();
        $this->assertCount(1, $assets, 'Count should be equal to 1');
        $this->assertEquals($description, $assets->first()->description);
        $assetId = $this->response->content();
        $this->call('GET', '/api/assets/' . $assetId . 1);
        $this->assertResponseStatus(404);
        $this->call('GET', '/api/assets/' . $assetId);
        $this->assertResponseOk();
        $this->assertContains('attachment', (string)$this->response);
        $this->call('DELETE', '/api/assets/' . $assetId . 1);
        $this->assertResponseStatus(404);
        $this->call('DELETE', '/api/assets/' . $assetId);
        $this->assertResponseOk();
        $assets = App\Asset::all();
        $this->assertCount(0, $assets, 'Count should be equal to 0');
    }

    protected function beforeApplicationDestroyed(callable $callback)
    {
        parent::beforeApplicationDestroyed($callback);
        File::cleanDirectory(storage_path('app/assets/'));
    }

}
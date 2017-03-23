<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */


    public function testImageStorage()
    {

        $file = new UploadedFile(storage_path('testing/image.jpg'), 'image.jpg', filesize(storage_path('testing/image.jpg')), 'image/jpg', null, true);

        //Store Images for user
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'user', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'USeR', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'USER', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'uSER', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 2, 'resource' => 'user', 'image' => $file])->assertResponseStatus(400);
        //Store Images for Group
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'group', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'gROUP', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'GROUP', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'groUP', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 2, 'resource' => 'grOUP', 'image' => $file])->assertResponseStatus(400);
        //Store Images for memo
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'memo', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'MEMO', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'memO', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 1, 'resource' => 'mEMO', 'image' => $file])->assertResponseStatus(201);
        $this->json('POST', '/store/image', ['id' => 1000, 'resource' => 'MEMo', 'image' => $file])->assertResponseStatus(400);
        $this->json('POST', '/store/image', ['id' => 1000, 'resource' => 'M3Mo', 'image' => $file])->assertResponseStatus(400);

    }

    public function testImageRetrieval()
    {
        $this->get('/retrieve/user/thumbnail/1')->assertResponseStatus(200);
        $this->get('/retrieve/user/thumbnail/9')->assertResponseStatus(400);
        $this->get('/retrieve/user/avatar/1')->assertResponseStatus(200);
        $this->get('/retrieve/user/avatar/9')->assertResponseStatus(400);

        $this->get('/retrieve/group/thumbnail/1')->assertResponseStatus(200);
        $this->get('/retrieve/group/thumbnail/9')->assertResponseStatus(400);
        $this->get('/retrieve/group/avatar/1')->assertResponseStatus(200);
        $this->get('/retrieve/group/avatar/9')->assertResponseStatus(400);

        $this->get('/retrieve/memo/thumbnail/1')->assertResponseStatus(200);
        $this->get('/retrieve/memo/thumbnail/9')->assertResponseStatus(400);
        $this->get('/retrieve/memo/avatar/1')->assertResponseStatus(200);
        $this->get('/retrieve/memo/avatar/9')->assertResponseStatus(400);
    }

    public function testImageDeletion()
    {
        $this->get('/delete/user/1')->assertResponseStatus(202);
        $this->get('/delete/group/1')->assertResponseStatus(202);
        $this->get('/delete/group/9')->assertResponseStatus(400);
        $this->get('/delete/memo/1')->assertResponseStatus(202);
        $this->get('/delete/memo/1000')->assertResponseStatus(400);

    }
}

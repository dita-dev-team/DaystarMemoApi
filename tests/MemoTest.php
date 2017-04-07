<?php
/**
 * Created by PhpStorm.
 * User: arin
 * Date: 3/9/17
 * Time: 11:14 AM
 */

use \Illuminate\Http\UploadedFile;

class MemoTest extends TestCase
{
    /*
     * Testing the Get Request for all memos
     */
    public function testGetMemos()
    {
        $this->get('/api/memos')->assertResponseStatus(200);
    }

    public function testPostMemos()
    {
        //Testing for proper parameters and saving
        $users = factory(App\User::class, 10)->create();
        $group = factory(App\Group::class, 10)->create();

//        User Tests
        $this->json('POST', '/api/memos', ['user_id' => $users->first()->id, 'memo_body' => 'Memo Test Assertion', 'to_user' => $users->first()->id])->assertResponseStatus(200);
        /*
         * Testing User saving including the file uploads.
         * Remember to copy another image in the directory storage/testing since the MemoController moves file to intended location
         * Rename the image image.jpg
         */
        $file = new UploadedFile(storage_path('testing/image.jpg'),'image.jpg', filesize(storage_path('testing/image.jpg')), 'image/jpg', null, true);
        $this->json('POST', '/api/memos', ['user_id' => $users->first()->id, 'memo_body' => 'Working on Coverage', 'to_user' => $users->first()->id, 'file' => $file])->assertResponseStatus(200);

        // Testing for wrong input or too few parameters
        $this->json('POST', 'api/memos')->assertResponseStatus(403);
        $this->json('POST', 'api/memos', ['user_id' => random_int(1,9)])->assertResponseStatus(403);
        $this->json('POST', 'api/memos', ['user_id' => random_int(1,9), 'memo_body' => 'Crazy Tests'])->assertResponseStatus(403);
        $this->json('POST', 'api/memos', ['user_id' => 'e', 'memo_body' => 'Tests huh!', 'to_user' => 'e'])->assertResponseStatus(403);
        $this->json('POST', 'api/memos', ['user_id' => random_int(11,20), 'memo_body' => 'Tests Again!', 'to_user' => random_int(20000,21000)])->assertResponseStatus(404);
        $this->json('POST', 'api/memos', ['user_id' => random_int(11, 20), 'memo_body' => 'Tests Lord Have Mercy!', 'to_user' => random_int(20000,21000)])->assertResponseStatus(404);

//        Group Tests
        $this->json('POST', '/api/memos', ['user_id' => $users->first()->id, 'memo_body' => 'Memo Test Assertion', 'to_group' => $group->first()->id])->assertResponseStatus(200);
        /*
         * Testing User saving including the file uploads.
         * Remember to copy another image in the directory storage/testing since the MemoController moves file to intended location
         * Rename the image image.jpg
         */
        $file = new UploadedFile(storage_path('testing/image_1.jpg'),'image.jpg', filesize(storage_path('testing/image_1.jpg')), 'image/jpg', null, true);
        $this->json('POST', '/api/memos', ['user_id' => $users->first()->id, 'memo_body' => 'Working on Coverage', 'to_group' => $group->first()->id, 'file' => $file])->assertResponseStatus(200);

        // Testing for wrong input or too few parameters
        $this->json('POST', 'api/memos')->assertResponseStatus(403);
        $this->json('POST', 'api/memos', ['user_id' => random_int(1,9)])->assertResponseStatus(403);
        $this->json('POST', 'api/memos', ['user_id' => random_int(1,9), 'memo_body' => 'Crazy Tests'])->assertResponseStatus(403);
        $this->json('POST', 'api/memos', ['user_id' => 'e', 'memo_body' => 'Tests huh!', 'to_group' => 'e'])->assertResponseStatus(403);
        $this->json('POST', 'api/memos', ['user_id' => random_int(11,20), 'memo_body' => 'Tests Again!', 'to_group' => random_int(20000,21000)])->assertResponseStatus(404);
        $this->json('POST', 'api/memos', ['user_id' => random_int(11, 20), 'memo_body' => 'Tests Lord Have Mercy!', 'to_group' => random_int(20000,21000)])->assertResponseStatus(404);

    }
}
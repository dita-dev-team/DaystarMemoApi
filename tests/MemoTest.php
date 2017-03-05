<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class MemoTest extends TestCase
{
    use DatabaseTransactions;

    public function testMemoCreation()
    {
        $group = factory(App\Group::class)->create();
        $user1 = factory(App\User::class)->create();
        $user2 = factory(App\User::class)->create();
        $memo = factory(App\Memo::class)->make();
        $memo->setSender($user1);
        $memo->setRecipient($user2);
        $memo->save();

        $memos = App\Memo::all();
        $this->assertCount(1, $memos, 'Count should be equal to 1');
        $result = $memos->first();
        $this->assertEquals($memo->body, $result->body, 'Should be equal');
        $this->assertEquals($user1->name, $result->from->name);
        $this->assertEquals($user2->name, $result->to->name);

        $memo->delete();
        $memos = App\Memo::all();
        $this->assertCount(0, $memos, 'Count should be equal to 0');
        $memo = factory(App\Memo::class)->make();
        $memo->setSender($user1);
        $memo->setRecipient($group);
        $memo->save();

        $memos = App\Memo::all();
        $this->assertCount(1, $memos, 'Count should be equal to 1');
        $result = $memos->first();
        $this->assertEquals($group->name, $result->to->name);

    }

    public function testMemoRoutes()
    {
        $group = factory(App\Group::class)->create();
        $user1 = factory(App\User::class)->create();
        $user2 = factory(App\User::class)->create();
        $memo = factory(App\Memo::class)->make();
        $this->call('POST', '/api/memos');
        $this->assertResponseStatus(400);
        $this->call('POST', '/api/memos', [
            'body' => $memo->body,
            'from' => $user1->id,
            'to' => $user2->id,
            'toGroup' => false,
            'isFile' => true
        ]);
        $this->assertResponseStatus(400);
        $this->call('POST', '/api/memos', [
            'body' => $memo->body,
            'from' => $user1->id + 10,
            'to' => $user2->id,
            'toGroup' => false
        ]);
        $this->assertResponseStatus(404);
        $this->call('POST', '/api/memos', [
            'body' => $memo->body,
            'from' => $user1->id,
            'to' => $user2->id + 10,
            'toGroup' => false
        ]);
        $this->assertResponseStatus(404);
        $this->call('POST', '/api/memos', [
            'body' => $memo->body,
            'from' => $user1->id,
            'to' => $group->id + 10,
            'toGroup' => true
        ]);
        $this->assertResponseStatus(404);
        $this->call('POST', '/api/memos', [
            'body' => $memo->body,
            'from' => $user1->id,
            'to' => $user2->id,
            'toGroup' => false
        ]);
        $this->assertResponseOk();

        $id = intval($this->response->content());

        $result = App\Memo::find($id);

        $this->assertEquals($memo->body, $result->body, 'Should be equal');
        $this->assertEquals($result->from->id, $user1->id, 'Should be equal');
        $this->assertEquals($result->to->id, $user2->id, 'Should be equal');
        $this->assertTrue($result->to instanceof \App\User, 'Should be a user');
        $this->json('GET', '/api/memos/' . ($id + 10))->seeStatusCode(404);
        $this->json('GET', '/api/memos/' . $id)
            ->seeStatusCode(200)
            ->seeJson([
                'id' => $id,
                'body' => $memo->body,
            ])
            ->dontSeeJson(['isFile']);

        $path = storage_path('testing/image.jpg');
        $name = 'image.jpg';
        $mimeType = 'image/jpeg';
        $size = 185134;
        $error = null;
        $test = true;
        $file = new UploadedFile($path, $name, $mimeType, $size, $error, $test);

        $this->call('POST', '/api/memos', [
            'body' => $memo->body,
            'from' => $user1->id,
            'to' => $user2->id,
            'toGroup' => false,
            'isFile' => true
        ], [], ['file' => $file]);
        $this->assertResponseOk();

        $id = intval($this->response->content());

        $this->json('GET', '/api/memos/' . $id)
            ->seeStatusCode(200)
            ->seeJson([
                'id' => $id,
                'body' => $memo->body,
            ]);
        $this->assertContains('filename', $this->response->content(), 'Should contain filename');
        $data = (array)json_decode($this->response->content());
        $id = $data['id'];
        $this->call('GET', '/api/files/' . ($id + 10));
        $this->assertResponseStatus(404);
        $this->call('GET', '/api/files/' . $id);
        $this->assertResponseOk();
        $this->assertContains('attachment', (string)$this->response);
    }

    protected function beforeApplicationDestroyed(callable $callback)
    {
        parent::beforeApplicationDestroyed($callback);
        File::cleanDirectory(storage_path('app/memos/'));
    }
}

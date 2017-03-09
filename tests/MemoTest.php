<?php
/**
 * Created by PhpStorm.
 * User: arin
 * Date: 3/9/17
 * Time: 11:14 AM
 */

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
        $this->json('POST', '/api/memos', ['user_id' => 1, 'memo_body' => 'Memo Test Assertion', 'to' => 1])->assertResponseStatus(200);
    }
}
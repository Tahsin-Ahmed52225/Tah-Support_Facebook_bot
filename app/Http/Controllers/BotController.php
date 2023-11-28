<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BotController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = env('BOT_VERIFY_TOKEN');
    }

    /**
     * verify Token
     */
    public function verifyToken()
    {
        $mode  = $request->get('hub_mode');
        $token = $request->get('hub_verify_token');

        if ($mode === "subscribe" && $this->token and $token === $this->token) {
            return response($request->get('hub_challenge'));
        }

        return response("Invalid token!", 400);
    }
    /**
     * handle Query of messenger bot
     */
    public function handleQuery()
    {
        $entry = $request->get('entry');

        $sender  = array_get($entry, '0.messaging.0.sender.id');
        // $message = array_get($entry, '0.messaging.0.message.text');

        $this->dispatchResponse($sender, 'Hello world. You can customise my response.');

        return response('', 200);
    }

    /**
     * dispatchResponse
     */
    private function dispatchResponse($id, $response)
    {
        $access_token = env('BOT_PAGE_ACCESS_TOKEN');
        $url = "https://graph.facebook.com/v2.6/me/messages?access_token={$access_token}";

        $data = json_encode([
            'recipient' => ['id' => $id],
            'message'   => ['text' => $response]
        ]);
    }
}

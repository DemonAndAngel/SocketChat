<?php

namespace App\Http\Controllers\Api;

use App\Message;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ApiHomeController extends Controller
{
    /**
     * 发送消息
     * @param Request $request
     * @return int
     */
    public function sendMessage(Request $request)
    {
        $message = new Message();
        $message->from_user_id=auth()->user()->id;
        $message->to_user_id=1;
        $message->is_receive=0;
        $message->messages=trim($request->input('message'));
        $message->save();
        if(!$message->messages)
            return 0;
        event(new \App\Events\ANewMessage($message));
        return 1;
    }
    public function offLineMessage()
    {
        $messages = Message::where('is_receive', 0)
            ->where('to_user_id',auth()->user()->id)
            ->orderBy('created_at')->get();
        if ($messages)
        {
            foreach($messages as $message)
            {
                event(new \App\Events\ANewMessage($message));
            }
        }
        $this->makeApiResponse([],'离线数据接收完毕！');
    }
    public function setMessage(Request $request){
        $message = Message::findOrFail($request->input('message_id'));
        $message->is_receive=1;
        $message->save();
        return $this->makeApiResponse([]);
    }
    public function getFriendList(){
        $user=auth()->user();
        $friendList=collect($user->fromFriends)->merge($user->toFriends);
        return $this->makeApiResponse($friendList);
    }
}

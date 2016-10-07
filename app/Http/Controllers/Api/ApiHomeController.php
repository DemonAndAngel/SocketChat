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
        $message->to_user_id=$request->input('to_user_id');
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
    public function getMessageListInfo(Request $request){
        $count=count(Message::where('is_receive', 0)
            ->where('to_user_id',auth()->user()->id)
            ->where('from_user_id',$request->input('from_user_id'))
            ->orderBy('created_at')->get());
        $message = Message::where('from_user_id',$request->input('from_user_id'))
            ->where('to_user_id',auth()->user()->id)
            ->orderBy('created_at','desc')->first();
        $message2 = Message::where('from_user_id',auth()->user()->id)
            ->where('to_user_id',$request->input('from_user_id'))
            ->orderBy('created_at','desc')->first();
        if(empty($message))
            $message=$message2;
        if($message&&$message2){
            if($message->created_at<$message2->created_at)
                $message=$message2;
        }
        return $this->makeApiResponse(compact('count','message'));
    }
    public function getMessageList(Request $request){
        $message = Message::with('fromUser','toUser')->where('from_user_id',$request->input('from_user_id'))
            ->where('to_user_id',auth()->user()->id)->get();
        $message2 = Message::with('fromUser','toUser')->where('from_user_id',auth()->user()->id)
            ->where('to_user_id',$request->input('from_user_id'))->get();
        $messages=collect($message)->merge($message2)->sortByDesc('created_at')->values()->all();
        return $this->makeApiResponse(compact('messages'));
    }
    public function setMessage(Request $request){
        $messages = Message::where('is_receive', 0)
            ->where('to_user_id',auth()->user()->id)
            ->where('from_user_id',$request->input('from_user_id'))
            ->orderBy('created_at')->get();
        foreach($messages as $message)
        {
            $message->is_receive=1;
            $message->save();
        }
        return $this->makeApiResponse([]);
    }
    public function setMessageForId(Request $request){
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

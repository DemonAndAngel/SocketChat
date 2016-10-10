<?php

namespace App\Http\Controllers\Api;

use App\Message;
use App\User;
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
        $messages=collect($message)->merge($message2)->sortBy('created_at')->values()->all();
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
        $friendList=collect($user->fromFriends()->where('is_friend',2)->get())->merge($user->toFriends()->where('is_friend',2)->get());
        return $this->makeApiResponse($friendList);
    }
    public function getUserInfo(Request $request){
        $users = User::where('name','like','%'.$request->input('friend_name').'%')->where('id','!=',auth()->user()->id)->limit(5)->get();
        return $this->makeApiResponse($users);
    }
    public function addFriend(Request $request){
        $self = User::where('id',auth()->user()->id)->first();
        $then = User::where('id',$request->input('friend_id'))->first();
        if(!$then)
            return $this->makeApiResponse([],'找不到用户',10001);
        $self->fromFriends()->attach($then->id);
        return $this->makeApiResponse([]);
    }
}

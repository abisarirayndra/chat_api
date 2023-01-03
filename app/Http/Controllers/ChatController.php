<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ChatResource;
use Auth;
use App\Models\User;
use App\Models\Chat;
use Validator;
use App\Events\ServerChatCreated;

class ChatController extends Controller
{
    public function index(){
        $account = Auth::user();
        $userList = User::where('id', '!=', Auth::id())->get();

        $dataCollect = collect([
            'myAccount' => $account,
            'user_list' => $userList,
        ]);
        return new ChatResource(true, 'Hello', $dataCollect);
    }

    public function chatWith(Request $request){
        $userClient = User::where('phone_number', $request->phone_number)->first();
        $chatSent = Chat::select('chats.id','chats.sender','users.name as recipient','chats.message','chats.created_at')
                        ->join('users','users.id','chats.recipient')
                        ->where('sender', Auth::user()->id)
                        ->where('recipient', $userClient->id)
                        ->get();
        $chatReceived = Chat::select('chats.id','users.name as sender','chats.recipient','chats.message','chats.created_at')
                        ->join('users','users.id','chats.sender')
                        ->where('sender', $userClient->id)
                        ->where('recipient', Auth::user()->id)
                        ->get();
        $chat_merged = $chatSent->merge($chatReceived);
        $chats = $chat_merged->sortBy([
            ['created_at','desc']
        ]);
        $chats->values()->all();
        return new ChatResource(true, null, $chats);
    }

    public function sendMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }elseif($request->phone_number == Auth::user()->phone_number){
            return response()->json([
                'message' => 'Cannot send to yourself'
            ], 422);
        }
        $userClient = User::where('phone_number', $request->phone_number)->first();
        $sent = Chat::create([
                    'sender' => Auth::user()->id,
                    'recipient' => $userClient->id,
                    'message' => $request->message,
                    'status' => false,
                ]);
        ServerChatCreated::dispatch($sent);
        return new ChatResource(true, 'Message Sent!', $sent);
    }
}

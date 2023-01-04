<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ChatResource;
use Auth;
use App\Models\User;
use App\Models\Chat;
use Validator;
use App\Events\ServerChatCreated;
use App\Models\ChatLogs;

class ChatController extends Controller
{
    // Function for registered user list
    public function index(){
        $userList = User::select('id','name','phone_number','photo')->where('id', '!=', Auth::id())
                            ->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'user_list' => $userList,
        ], 200);
    }

    //Function for show the latest message and unread chat
    public function latestConversation(){
        $latest = ChatLogs::join('chats','chats.id','chat_logs.latest_message')
                            ->join('users','users.id','chats.sender')
                            ->select('users.name as sender','users.phone_number','chats.message','chat_logs.unread_count','chat_logs.created_at')
                            ->where('chat_logs.recipient', Auth::user()->id)
                            ->orderBy('chat_logs.created_at','desc')
                            ->get();

        return response()->json(['success' => true, 'latest_conversation' => $latest], 200);
    }

    // Function for show we chat with spesific user (include, sent and received message)
    public function chatWith(Request $request){
        $userClient = User::select('id','name','phone_number','photo')->where('phone_number', $request->phone_number)->first();
        if(Auth::user()->phone_number == $request->phone_number){
            return response()->json([
                'success' => false,
                'message' => "You can't chat with yourself"
            ], 404);
        }
        $readChats = Chat::where('sender', $userClient->id)
                            ->where('recipient', Auth::user()->id)
                            ->where('status', false)
                            ->update([
                                'status' => true,
                            ]);
        $logs = ChatLogs::where('sender', $userClient->id)
                            ->where('recipient', Auth::user()->id)
                            ->update([
                                'unread_count' => 0,
                            ]);
        $chatSent = Chat::select('chats.id','chats.sender','users.name as recipient','chats.message','chats.status','chats.created_at')
                        ->join('users','users.id','chats.recipient')
                        ->where('sender', Auth::user()->id)
                        ->where('recipient', $userClient->id)
                        ->get();
        $chatReceived = Chat::select('chats.id','users.name as sender','chats.recipient','chats.message','chats.status','chats.created_at')
                        ->join('users','users.id','chats.sender')
                        ->where('sender', $userClient->id)
                        ->where('recipient', Auth::user()->id)
                        ->get();
        $chat_merged = $chatSent->merge($chatReceived);
        $chats = $chat_merged->sortBy([['created_at','desc']]);
        $chats->values()->all();
        return response()->json([
            'success' => true,
            'user_client' => $userClient,
            'data' => $chats,
        ], 200);
    }

    // function for send the message to spesific user
    public function sendMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }elseif($request->phone_number == Auth::user()->phone_number){
            return response()->json([
                'message' => 'Cannot send to yourself'
            ], 404);
        }
        $userClient = User::where('phone_number', $request->phone_number)->first();
        $sent = Chat::create([
                    'sender' => Auth::user()->id,
                    'recipient' => $userClient->id,
                    'message' => $request->message,
                    'status' => false,
                ]);
        $chatSent = Chat::select('chats.id','chats.sender','users.name as recipient','chats.message','chats.status','chats.created_at')
                ->join('users','users.id','chats.recipient')
                ->where('sender', Auth::user()->id)
                ->where('recipient', $userClient->id)
                ->get();
        $chatReceived = Chat::select('chats.id','users.name as sender','chats.recipient','chats.message','chats.status','chats.created_at')
                        ->join('users','users.id','chats.sender')
                        ->where('sender', $userClient->id)
                        ->where('recipient', Auth::user()->id)
                        ->get();
        $chat_merged = $chatSent->merge($chatReceived);
        $chats = collect($chat_merged->sortBy([['created_at','desc']]))->first();
        $log = ChatLogs::where('sender', Auth::user()->id)
                        ->where('recipient', $userClient->id)
                        ->first();
        if(!$log){
            $unreadChats = Chat::where('recipient', $userClient->id)
                                ->where('sender', Auth::user()->id)
                                ->where('status', 0)
                                ->count();
            ChatLogs::create([
                'sender' => Auth::user()->id,
                'recipient' => $userClient->id,
                'latest_message' => $chats->id,
                'unread_count' => $unreadChats,
            ]);

            ChatLogs::create([
                'sender' => $userClient->id,
                'recipient' => Auth::user()->id,
                'latest_message' => $chats->id,
                'unread_count' => 0,
            ]);
        }else{
            $unreadChats = Chat::where('recipient', $userClient->id)
                                ->where('sender', Auth::user()->id)
                                ->where('status', 0)
                                ->count();
            ChatLogs::where('sender', Auth::user()->id)
                            ->where('recipient', $userClient->id)
                            ->update([
                               'latest_message' => $chats->id,
                            ]);
            ChatLogs::where('sender', $userClient->id  )
                            ->where('recipient', Auth::user()->id)
                            ->update([
                               'latest_message' => $chats->id,
                            ]);
            ChatLogs::where('sender', Auth::user()->id)
                        ->where('recipient', $userClient->id)
                        ->update([
                            'unread_count' => $unreadChats,
                        ]);
        }
        ServerChatCreated::dispatch($sent);
        return response()->json([
            'success' => true,
            'message' => 'Message Sent Success!',
            'data' => $sent,
        ], 201);
    }
}

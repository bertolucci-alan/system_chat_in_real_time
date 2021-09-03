<?php

namespace App\Http\Controllers\Api;

use App\Events\Chat\SendMessage;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function list(User $user)
    {
        $userFrom = Auth::user()->id;
        $userTo = $user->id;

        /*
        | pegando mensagem de from para to e de to para from
        | 
        | [from = $userFrom && to = $userTo]
        |            --OR--
        | [from = $userTo && to = $userFrom]
        */

        $messages = Message::where(
            function ($query) use ($userFrom, $userTo) {
                $query->where([
                    'from' => $userFrom,
                    'to' => $userTo,
                ]);
            }
        )->orWhere(
            function ($query) use ($userTo, $userFrom) {
                $query->where([
                    'from' => $userTo,
                    'to' => $userFrom,
                ]);
            }
        )->orderBy('created_at', 'ASC')->get();

        return response()->json([
            'messages' => $messages
        ], status: Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $message = Message::create([
            "content" => $request->content,
            "to" => filter_var($request->to, FILTER_SANITIZE_STRING),
            "from" => Auth::user()->id
        ]);

        Event::dispatch(new SendMessage($message, $request->to));
    }
}
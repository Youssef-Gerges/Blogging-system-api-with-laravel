<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;

class SubscribController extends Controller
{
    public function subscrib(Request $request)
    {
        $request->validate([
            'user_id' => 'integer|required'
        ]);

        User::findOrFail($request->user_id);

        if (Subscriber::where('user_id', $request->user_id)->first()) {
            return response()->json([
                'message' => 'User already subscribed'
            ]);
        }
        $subscriber = new Subscriber();
        $subscriber->user_id = $request->user_id;
        $subscriber->save();
        return response()->json([
            'message' => 'User subscribed successfullty'
        ]);
    }

    public function unSubscrib(Request $request)
    {
        $request->validate([
            'user_id' => 'integer|required'
        ]);

        User::findOrFail($request->user_id);
        $subscriber = Subscriber::where('user_id', $request->user_id)->first();
        if (!$subscriber) {
            return response()->json([
                'message' => 'User not subscribed'
            ]);
        }
        $subscriber->delete();
        return response()->json([
            'message' => 'User Unsubscribed successfullty'
        ]);
    }
}

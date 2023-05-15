<?php

namespace nicotc\SessionChecker;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionChecker
{
    public static function check()
    {
        if (config('session.driver') !== 'database') {
            return;
        }
        $userId = Auth::id();
        $session = DB::table('sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '>=', time() - config('session.lifetime') * 60)
            ->first();
        if ($session) {
            $sessionId = $session->id;
            DB::table('sessions')->where('id', $sessionId)->delete();
            Session::getHandler()->destroy($sessionId);
            session()->flash('status', 'Se ha cerrado una session anterior.');
        }
        Auth::loginUsingId($userId);
        return redirect()->intended('/');
    }
}

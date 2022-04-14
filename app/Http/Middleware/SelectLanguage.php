<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SelectLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $this->set_lang();
        return $next($request);
    }
    private function set_lang(){
        $lang=$_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $temp=strlen($lang);
        $array=["el","en"];
        foreach ($array as $value){
            $numb=strpos ( $lang , $value );
            if (is_numeric($numb))
                if ($numb< $temp)
                    $temp=$numb;

        }
        if ($temp != strlen($lang))
            $lang=substr($lang, $temp+0, $temp+2);
        else
            $lang="el";
        App::setLocale($lang);
    }
}

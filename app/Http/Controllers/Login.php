<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Helpers\SysUtils;
use App\Helpers\UserLogin;

class Login extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view('site-login', [
            'PAGE_TITLE' => 'Login',
        ]);
    }

    public function doLogin(Request $request)
    {
        $form = $request->only(['f-password']);
        if (!SysUtils::checkLogin($form['f-password'] ?? '')) {
            return $this->redirectWithError('site.login', 'Senha inválida!');
        }

        $User = UserLogin::login();
        SysUtils::loginUser($User);
        if (!Auth::check()) {
            return $this->redirectWithError('site.login', 'Erro ao logar usuário!');
        }

        return redirect()->route('site.login');
    }

    // Auth::id()
}

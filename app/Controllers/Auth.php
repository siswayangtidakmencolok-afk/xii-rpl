<?php
namespace App\Controllers;

class Auth extends BaseController {
    public function login() {
        return view('auth/login'); // view ada di app/Views/auth/login.php
    }

    public function process() {
        // validasi login, misalnya cek username/password
    }
}

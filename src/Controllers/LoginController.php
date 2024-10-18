<?php

namespace App\Controllers;

use App\Models\User;

class LoginController extends BaseController
{
    public function showForm() {
        return $this->render('login-form');
    }

    public function login() {
        session_start();
        $_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? 0;

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $errors = empty($username) || empty($password) ? ["Both fields are required."] : [];

        if (!$errors && !$this->attemptLogin($username, $password, $errors)) {
            $_SESSION['login_attempts']++;
            if ($this->isFormDisabled()) $errors[] = "Too many failed login attempts. The form is now disabled.";
        }

        return empty($errors) ? header("Location: /welcome") : $this->render('login-form', ['errors' => $errors, 'disabled' => $this->isFormDisabled()]);
    }

    public function welcome() {
        session_start();
        if (!$this->isLoggedIn()) header("Location: /login-form") && exit();
        return $this->render('welcome', ['users' => (new User())->getAllUsers()]);
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /login-form");
        exit();
    }

    private function attemptLogin($username, $password, &$errors) {
        try {
            if ($hashedPassword = (new User())->getPassword($username)) {
                if (password_verify($password, $hashedPassword)) {
                    $_SESSION['is_logged_in'] = $_SESSION['user_id'] = $username;
                    $_SESSION['login_attempts'] = 0;
                    return true;
                }
            }
            $errors[] = "Invalid username or password.";
        } catch (Exception $e) {
            $errors[] = "An error occurred: " . $e->getMessage();
        }
        return false;
    }

    private function isLoggedIn() {
        return !empty($_SESSION['is_logged_in']);
    }

    private function isFormDisabled() {
        return $_SESSION['login_attempts'] >= 3;
    }
}

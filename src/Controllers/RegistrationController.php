<?php

namespace App\Controllers;

use App\Models\User;

class RegistrationController extends BaseController
{
    public function showForm()
    {
        return $this->render('registration-form');
    }

    public function register()
    {

        $errors = $this->validateRegistration($_POST);
        if ($errors) {
            return $this->render('registration-form', array_merge($errors, $_POST));
        }

        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
        $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        try {
            $user = new User();
            if ($user->save($username, $email, $first_name, $last_name, $password) > 0) {
                return $this->render('registration-success');
            } else {
                echo "There was an error during registration. Please try again.";
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    private function validateRegistration($data)
    {
        $password = $data['password'] ?? '';
        $confirm_password = $data['confirm_password'] ?? '';

        $errors = [];
        if (empty($data['username']) || empty($data['email']) || empty($password) || empty($confirm_password)) {
            $errors['errors'][] = "All required fields must be filled out.";
        }
        if (strlen($password) < 8)
            $errors['errors'][] = "Password must be at least 8 characters long.";
        if (!preg_match('/[0-9]/', $password))
            $errors['errors'][] = "Password must contain at least one numeric character.";
        if (!preg_match('/[a-zA-Z]/', $password))
            $errors['errors'][] = "Password must contain at least one non-numeric character.";
        if (!preg_match('/[\W]/', $password))
            $errors['errors'][] = "Password must contain at least one special character (!@#$%^&*-+).";
        if ($password !== $confirm_password)
            $errors['errors'][] = "Passwords do not match.";

        return $errors;
    }
}

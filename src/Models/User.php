<?php

namespace App\Models;

use App\Models\BaseModel;
use \PDO;

class User extends BaseModel
{
    public function save($username, $email, $first_name, $last_name, $password) {
        $sql = "INSERT INTO users (username, email, first_name, last_name, password_hash) VALUES (:username, :email, :first_name, :last_name, :password_hash)";
        $statement = $this->db->prepare($sql);
    
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        // Bind parameters
        $statement->bindParam(':username', $username);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':first_name', $first_name);
        $statement->bindParam(':last_name', $last_name);
        $statement->bindParam(':password_hash', $hashed_password);
    
        // Execute
        $statement->execute();
    
        return $statement->rowCount();
    }
    

    public function getAllUsers() {
        return $this->fetchAll("SELECT id, first_name, last_name, email FROM users");
    }

    public function getPassword($username) {
        return $this->fetchColumn("SELECT password_hash FROM users WHERE username = :username", ['username' => $username]);
    }

    public function getData() {
        return $this->fetchAll("SELECT * FROM users", [], '\App\Models\User');
    }

    private function fetchAll($query, $params = [], $class = PDO::FETCH_ASSOC) {
        $statement = $this->db->prepare($query);
        $statement->execute($params);
        return $statement->fetchAll($class);
    }

    private function fetchColumn($query, $params = []) {
        $statement = $this->db->prepare($query);
        $statement->execute($params);
        return $statement->fetchColumn();
    }
}

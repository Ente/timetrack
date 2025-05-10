<?php

namespace Toil;

use Arbeitszeit\Exceptions;

class Tokens extends Routes {

    private $arbeit;

    public function __construct(){
        $this->arbeit = new \Arbeitszeit\Arbeitszeit();
    }

    public function createToken($username, $expiration = 7200, $isUpdate = false){
        Exceptions::error_rep("[API] Creating token for user '{$username}'...");
        $user = $this->arbeit->benutzer()->get_user($username);
        if($user){
            $token = bin2hex(random_bytes(16));
            $refresh_token = bin2hex(random_bytes(16));
            $expires_at = date("Y-m-d H:i:s", time() + $expiration);
            $created_at = date("Y-m-d H:i:s");
            $updated_at = date("Y-m-d H:i:s");
            $user_id = $user['id'];
            if($isUpdate){
                $this->update_token($user['id'], $token, $refresh_token, $expires_at, $created_at, $updated_at);
            } else {
                $this->create_token($user['id'], $token, $refresh_token, $expires_at, $created_at, $updated_at);
            }
            return [
                'access_token' => $token,
                'refresh_token' => $refresh_token,
                'expires_at' => $expires_at,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
                'user_id' => $user_id,
                'username' => $username,
                'status' => 'success',
                'isUpdate' => $isUpdate
            ];
        } else {
            Exceptions::error_rep("[API] User '{$username}' not found.");
            return false;
        }
    }

    public function create_token($user_id, $token, $refresh_token, $expires_at, $created_at, $updated_at){
        Exceptions::error_rep("[API] Creating token in database...");
        $query = "INSERT INTO tokens (user_id, access_token, refresh_token, expires_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->arbeit->db()->sendQuery($query);
        $stmt->execute([$user_id, $token, $refresh_token, $expires_at, $created_at, $updated_at]);
        Exceptions::error_rep("[API] Token created successfully.");
        return [
            'status' => 'success',
            'message' => 'Token created successfully.'
        ];
    }

    public function update_token($user_id, $token, $refresh_token, $expires_at, $created_at, $updated_at){
        Exceptions::error_rep("[API] Updating token in database...");
        $query = "UPDATE tokens SET access_token = ?, refresh_token = ?, expires_at = ?, created_at = ?, updated_at = ? WHERE user_id = ?";
        $stmt = $this->arbeit->db()->sendQuery($query);
        $stmt->execute([$token, $refresh_token, $expires_at, $created_at, $updated_at, $user_id]);
        Exceptions::error_rep("[API] Token updated successfully.");
        return [
            'status' => 'success',
            'message' => 'Token updated successfully.'
        ];
    }

    public function delete_token($user_id, $token){
        Exceptions::error_rep("[API] Deleting token from database...");
        $query = "DELETE FROM tokens WHERE user_id = ? AND access_token = ?";
        $stmt = $this->arbeit->db()->sendQuery($query);
        $stmt->execute([$user_id, $token]);
        Exceptions::error_rep("[API] Token deleted successfully.");
        return [
            'status' => 'success',
            'message' => 'Token deleted successfully.'
        ];
    }

    public function get_token_from_user_id($user_id){
        Exceptions::error_rep("[API] Getting token from database...");
        $query = "SELECT * FROM tokens WHERE user_id = ?";
        $stmt = $this->arbeit->db()->sendQuery($query);
        $stmt->execute([$user_id]);
        $token = $stmt->fetch();
        if($token){
            Exceptions::error_rep("[API] Token found.");
            return $token;
        } else {
            Exceptions::error_rep("[API] Token not found.");
            return false;
        }
    }

    public function get_token_from_token($token){
        Exceptions::error_rep("[API] Getting token from database...");
        $query = "SELECT * FROM tokens WHERE access_token = ?";
        $stmt = $this->arbeit->db()->sendQuery($query);
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch();
        if($tokenData){
            Exceptions::error_rep("[API] Token found.");
            return $tokenData;
        } else {
            Exceptions::error_rep("[API] Token not found.");
            return false;
        }
    }

    public function validate_token($token){
        Exceptions::error_rep("[API] Validating token...");
        $query = "SELECT * FROM tokens WHERE access_token = ?";
        $stmt = $this->arbeit->db()->sendQuery($query);
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch();
        if($tokenData){
            if(strtotime($tokenData['expires_at']) > time()){
                Exceptions::error_rep("[API] Token is valid.");
                return true;
            } else {
                Exceptions::error_rep("[API] Token is expired.");
                return false;
            }
        } else {
            Exceptions::error_rep("[API] Token not found.");
            return false;
        }
    }
    
    public function refresh_token($refresh_token){
        Exceptions::error_rep("[API] Refreshing token...");
        $query = "SELECT * FROM tokens WHERE refresh_token = ?";
        $stmt = $this->arbeit->db()->sendQuery($query);
        $stmt->execute([$refresh_token]);
        $tokenData = $stmt->fetch();
        if($tokenData){
            if(strtotime($tokenData['expires_at']) > time()){
                Exceptions::error_rep("[API] Refresh token is still valid.");
                $new_access_token = bin2hex(random_bytes(16));
                $new_refresh_token = bin2hex(random_bytes(16));
                $new_expires_at = date("Y-m-d H:i:s", time() + 7200);
                $updated_at = date("Y-m-d H:i:s");

                $this->update_token(
                    $tokenData['user_id'],
                    $new_access_token,
                    $new_refresh_token,
                    $new_expires_at,
                    $tokenData['created_at'],
                    $updated_at
                );

                Exceptions::error_rep("[API] Token refreshed successfully.");
                return [
                    'access_token' => $new_access_token,
                    'refresh_token' => $new_refresh_token,
                    'expires_at' => $new_expires_at,
                    'updated_at' => $updated_at,
                    'user_id' => $tokenData['user_id'],
                    'status' => 'success'
                ];
            } else {
                Exceptions::error_rep("[API] Refresh token is expired.");
                return false;
            }
        } else {
            Exceptions::error_rep("[API] Refresh token not found.");
            return false;
        }
    }
}
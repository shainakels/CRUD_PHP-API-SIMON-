<?php

interface CrudInterface{
    public function getAll();
    public function getOne();
    public function insert();
    public function update();
    public function delete();
}

class Crud_model{

    protected $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

//GET ALL
    public function getAll(){
        $sql = "SELECT * FROM users";
        try{
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute()){
                $data =  $stmt->fetchAll();
                if ($stmt->rowCount() > 0){
                    return $data;
                }else{
                    return 'There are no data present';
                }
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    } 

//GET ONE
    public function getOne($data){
        $sql = "SELECT * FROM users WHERE User_ID = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->User_ID])){
                $data =  $stmt->fetchAll();
                if ($stmt->rowCount() > 0){
                    return $data;
                }else{
                    return 'User does not exist';
                }
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }

//INSERT
    public function insert($data) {
        if (!isset($data->FirstName) || !isset($data->LastName)) {
            return ["status" => "error", "message" => "FirstName and LastName are kailangan na fields."];
        }
    
        if (empty($data->FirstName) || empty($data->LastName)) {
            return ["status" => "error", "message" => "FirstName and LastName ay bawal empty."];
        }
    
        $isAdmin = isset($data->is_Admin) ? $data->is_Admin : 0;
    
        $sql = 'INSERT INTO users (FirstName, LastName, is_Admin) VALUES (?, ?, ?)';
        
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->FirstName, $data->LastName, $isAdmin])) {
                $lastInsertedId = $this->pdo->lastInsertId(); 
             
                $fetchSql = "SELECT * FROM users WHERE User_ID = ?";
                $fetchStmt = $this->pdo->prepare($fetchSql);
                if ($fetchStmt->execute([$lastInsertedId])) {
                    $insertedData = $fetchStmt->fetch();
                    return [
                        "status" => "success",
                        "message" => "Data ay matagumpay na nailagay.",
                        "data" => $insertedData
                    ];
                } else {
                    return [
                        "status" => "error",
                        "message" => "Failed to retrieve ang nilagay na data."
                    ];
                }
            } else {
                return ["status" => "error", "message" => "Ang data ay hindi matagumpay na nailagay."];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }    

//UPDATE
    public function update($data) {
        if (!isset($data->User_ID)) {
            return ["status" => "error", "message" => "User_ID ay required."];
        }
    
        $sql = "UPDATE users SET is_Admin = CASE WHEN is_Admin = 0 THEN 1 WHEN is_Admin = 1 THEN 0 END WHERE User_ID = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->User_ID])) {
                if ($stmt->rowCount() > 0) {
                    
                    $updatedDataSql = "SELECT * FROM users WHERE User_ID = ?";
                    $updatedStmt = $this->pdo->prepare($updatedDataSql);
                    if ($updatedStmt->execute([$data->User_ID])) {
                        $updatedData = $updatedStmt->fetch();
                        return [
                            "status" => "success",
                            "message" => "Data ay matagumpay na updated.",
                            "data" => $updatedData
                        ];
                    } else {
                        return [
                            "status" => "error",
                            "message" => "Hindi matagumpay ang pagkuha ng data."
                        ];
                    }
                } else {
                    return [
                        "status" => "error",
                        "message" => "Walang pagbabago na ginawa at ang user ay wala."
                    ];
                }
            } else {
                return ["status" => "error", "message" => "Hindi matagumpay ang update sa data."];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

//DELETE
public function delete($data) {
    $sql = "DELETE FROM users WHERE User_ID = ?";

    try {
        $stmt = $this-> pdo->prepare($sql);
        if ($stmt->execute([$data->User_ID])) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(value: ["message"=>"Ang user ay matagumpay na deleted"]);
            } else {
                http_response_code(response_code: 404);
                echo json_encode(value: ["message"=>"Ang user ay wala o ito ay deleted na"]);
            }
        }
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}
}

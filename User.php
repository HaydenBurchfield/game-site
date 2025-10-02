<?php
require_once 'DatabaseConnection.php'; 

class User {
    // Fields (match database columns)
    public $id = 0;
    public $username = "";
    public $password = "";
    public $email = "";
    public $create_date = "";

    // Search users
    public static function searchUsers($searchTerm) {
        $db = new DatabaseConnection();
        $sql = "SELECT * FROM `user` WHERE username LIKE ? OR email LIKE ?";
        $stmt = $db->connection->prepare($sql);
        if (!$stmt) die("Error preparing: " . $db->connection->error);

        $searchPattern = "%$searchTerm%";
        $stmt->bind_param("ss", $searchPattern, $searchPattern);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        $stmt->close();
        $db->closeConnection();
        return $users;
    }

    // Get all users
    public static function getAllUsers() {
        $db = new DatabaseConnection();
        $sql = "SELECT * FROM `user` ORDER BY create_date DESC";
        $stmt = $db->connection->prepare($sql);
        if (!$stmt) die("Error preparing: " . $db->connection->error);

        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        $stmt->close();
        $db->closeConnection();
        return $users;
    }

    // Populate user by ID
    public function populate($id) {
        $db = new DatabaseConnection();
        $sql = "SELECT * FROM `user` WHERE id=?";
        $stmt = $db->connection->prepare($sql);
        if (!$stmt) die("Error preparing: " . $db->connection->error);

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->email = $row['email'];
            $this->create_date = $row['create_date'];
        }

        $stmt->close();
        $db->closeConnection();
    }

    // Insert new user
    public function insert() {
        $db = new DatabaseConnection();
        $sql = "INSERT INTO `user` (username, password, email, create_date) VALUES (?, ?, ?, NOW())";
        $stmt = $db->connection->prepare($sql);
        if (!$stmt) die("Error preparing: " . $db->connection->error);

        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $this->username, $hashedPassword, $this->email);

        $success = $stmt->execute();
        if ($success) $this->id = $stmt->insert_id;

        $stmt->close();
        $db->closeConnection();
        return $success;
    }

    public function update() {
    $db = new DatabaseConnection();

    if (!empty($this->password)) {
        // Hash new password if updating
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = "UPDATE `user` SET username = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $db->connection->prepare($sql);
        if (!$stmt) {
            die("Error preparing update: " . $db->connection->error);
        }
        $stmt->bind_param("sssi", $this->username, $this->email, $hashedPassword, $this->id);
    } else {
        // Update without password
        $sql = "UPDATE `user` SET username = ?, email = ? WHERE id = ?";
        $stmt = $db->connection->prepare($sql);
        if (!$stmt) {
            die("Error preparing update: " . $db->connection->error);
        }
        $stmt->bind_param("ssi", $this->username, $this->email, $this->id);
    }

    $success = $stmt->execute();
    $stmt->close();
    $db->closeConnection();
    return $success;
    }


    // Delete user
    public static function deleteUser($id) {
        $db = new DatabaseConnection();
        $sql = "DELETE FROM `user` WHERE id=?";
        $stmt = $db->connection->prepare($sql);
        if (!$stmt) die("Error preparing: " . $db->connection->error);

        $stmt->bind_param("i", $id);
        $success = $stmt->execute();

        $stmt->close();
        $db->closeConnection();
        return $success;
    }

    // Validate login
    public static function validateUser($email, $password) {
        $db = new DatabaseConnection();
        $sql = "SELECT * FROM `user` WHERE email=?";
        $stmt = $db->connection->prepare($sql);
        if (!$stmt) die("Error preparing: " . $db->connection->error);

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $userId = 0;
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $userId = $row['id'];
            }
        }

        $stmt->close();
        $db->closeConnection();
        return $userId;
    }
}
?>

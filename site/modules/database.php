<?php
class Database {
    private $db;

    public function __construct($path) {
        try {
            $this->db = new PDO("sqlite:" . $path);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function Execute($sql) {
        try {
            $this->db->exec($sql);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Execute failed: " . $e->getMessage());
        }
    }

    public function Fetch($sql) {
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Fetch failed: " . $e->getMessage());
        }
    }

    public function Create($table, $data) {
        try {
            $columns = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
            $stmt = $this->db->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Create failed: " . $e->getMessage());
        }
    }

    public function Read($table, $id) {
        try {
            $sql = "SELECT * FROM $table WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Read failed: " . $e->getMessage());
        }
    }

    public function Update($table, $id, $data) {
        try {
            $set = [];
            foreach ($data as $key => $value) {
                $set[] = "$key = :$key";
            }
            $set = implode(", ", $set);
            $sql = "UPDATE $table SET $set WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Update failed: " . $e->getMessage());
        }
    }

    public function Delete($table, $id) {
        try {
            $sql = "DELETE FROM $table WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Delete failed: " . $e->getMessage());
        }
    }

    public function Count($table) {
        try {
            $sql = "SELECT COUNT(*) as count FROM $table";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            throw new Exception("Count failed: " . $e->getMessage());
        }
    }
}

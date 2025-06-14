<?php

class ContactDetails
{
    private $conn;
    private $table = 'contact_details';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function get()
    {
        $query = "SELECT * FROM {$this->table} LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data)
    {
        $query = "UPDATE {$this->table} SET phone = :phone, email = :email, address = :address";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':address', $data['address']);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}

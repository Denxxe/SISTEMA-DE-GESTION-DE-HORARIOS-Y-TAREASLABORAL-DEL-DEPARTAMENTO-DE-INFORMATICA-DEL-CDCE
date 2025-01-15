<?php

require_once("./configurarBD.php");

class ORM {
    private $pdo;
    private $table;

    public function __construct($table) {

        $this->table = $table;

        try
        {
            $datos_servidor=MANEJADOR.':host='.SERVIDOR.';dbname='.BD;
            $this->pdo = new PDO($datos_servidor, USUARIO, CLAVE);
            $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        }
        catch(PDOException $e)
        {
            echo $e->getMessage()." --- ".$e->getLine();
        }
    }

    // CREATE
    public function insert($data) {
        $columns = implode(", ", array_keys($data));
        $placeholder = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholder)";
        $stmt = $this->pdo->prepare($sql);

        // Vincular los valores a los placeholders con bindValue
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    }

    // READ
    public function getAll() {
        try{
            $sql = "SELECT * FROM {$this->table}";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            echo $e->getMessage()." --- ".$e->getLine();
        }
    }

    // UPDATE
    public function update($data, $id) {
        try{
            $set = "";
            foreach ($data as $key => $value) {
                $set .= "$key = :$key, ";
            }
            $set = rtrim($set, ", ");
            $sql = "UPDATE {$this->table} SET $set WHERE id = :id";
            $data['id'] = $id;
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($data);
        }
        catch(PDOException $e)
        {
            echo $e->getMessage()." --- ".$e->getLine();
        }
    }

    // DELETE
    public function borrar($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
?>

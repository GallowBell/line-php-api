<?php 

//php class connection pdo
class Connection{
    //set default value from $_ENV
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset;
    private $pdo;
    private $debug_mode;

    /**
     * @param array $data
     * @return void
     */
    public function __construct($data = [
        'DB_HOST' => null,
        'DB_NAME' => null,
        'DB_USERNAME' => null,
        'DB_PASSWORD' => null,
        'DB_CHARSET' => null,
        'DEBUG_MODE' => null,
    ]){
        $this->host = $data['DB_HOST']?$data['DB_HOST']:$_ENV['DB_HOST'];
        $this->dbname = $data['DB_NAME']?$data['DB_NAME']:$_ENV['DB_NAME'];
        $this->username = $data['DB_USERNAME']?$data['DB_USERNAME']:$_ENV['DB_USERNAME'];
        $this->password = $data['DB_PASSWORD']?$data['DB_PASSWORD']:$_ENV['DB_PASSWORD'];
        $this->charset = $data['DB_CHARSET']?$data['DB_CHARSET']:$_ENV['DB_CHARSET'];
        $this->debug_mode = $data['DEBUG_MODE']?$data['DEBUG_MODE']:$_ENV['DEBUG_MODE'];
    }

    /**
     * @return PDO|boolean
     */
    public function connect(){
        try{
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=$this->charset", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        }catch(PDOException $e){

            if($this->debug_mode){
                echo "Connection failed: " . $e->getMessage();
            }
            return false;
            
        }
    }

    /**
     * @return void
     */
    //add function disconnect
    public function disconnect(){
        $this->pdo = null;
    }

    /**
     * @return PDO
     */
    //add function get pdo
    public function getPDO(){
        return $this->pdo;
    }

    //function query
    /**
     * @param string $sql
     * @return array|boolean
     */
    public function query($sql){
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //function select and prepare
    /**
     * @param string $sql
     * @param array $params
     * @return array|boolean
     */
    public function select($sql, $params = []){
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            if($this->debug_mode){
                echo "Connection failed: " . $e->getMessage();
            }
            return false;
            
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array|boolean
     */
    //function insert and prepare
    public function insert($sql, $params = []){
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $this->pdo->lastInsertId()?$this->pdo->lastInsertId():true;

        }catch(PDOException $e){
            if($this->debug_mode){
                echo "Connection failed: " . $e->getMessage();
            }
            return false;
            
        }
    }

    //function update prepare
    /**
     * @param string $sql
     * @param array $params
     * @return int|boolean row count affected
     */
    public function update($sql, $params = []){
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            //if rowCount() not available return true
            /* if(method_exists($stmt, 'rowCount')){
                return $stmt->rowCount();
            }else{
                return true;
            } */
            return true;

        }catch(PDOException $e){

            if($this->debug_mode){
                echo "Connection failed: " . $e->getMessage();
                return false;
            }

            return false;
            
        }
    }

    //function delete prepare
    /**
     * @param string $sql
     * @param array $params
     * @return int|boolean row count affected
     */public function delete($sql, $params = []){
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            //return boolean
            if(method_exists($stmt, 'rowCount')){
                return $stmt->rowCount();
            }else{
                return true;
            }

        }catch(PDOException $e){
            if($this->debug_mode){
                echo "Connection failed: " . $e->getMessage();
            }
            return false;
            
        }
    }

    public function beginTransaction() {
        try {
            $this->pdo->beginTransaction();
            return true;
        } catch (PDOException $e) {
            if($this->debug_mode){
                echo "Connection failed: " . $e->getMessage();
            }
            return false;
        }
    }
    
    public function commit() {
        try {
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            if($this->debug_mode){
                echo "Connection failed: " . $e->getMessage();
            }
            return false;
        }
    }
    
    public function rollBack() {
        try {
            $this->pdo->rollBack();
            return true;
        } catch (PDOException $e) {
            if($this->debug_mode){
                echo "Connection failed: " . $e->getMessage();
            }
            return false;
        }
    }
}

?>

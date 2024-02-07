<?php 


/**
 * Connection PDO Class parameter Array
* * DB_HOST - host of database
* * DB_NAME - name of database
* * DB_USERNAME - username of database
* * DB_PASSWORD - password of database
* * DB_CHARSET - charset of database
* * DEBUG_MODE - debug mode to show error whene occur
* * REDIS_HOST - host of redis
* * REDIS_PORT - port of redis
 */
class Connection{
    
    //set default value from $_ENV

    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset;
    private $pdo;
    private $debug_mode;
    private $redis;
    private $redis_host;
    private $redis_port;
    private $cached_key;
    private $cached_time = 60;

    /**
     * @param array $data
     * * DB_HOST host of database
     * * DB_NAME name of database
     * * DB_USERNAME username of database
     * * DB_PASSWORD password of database
     * * DB_CHARSET charset of database
     * * DEBUG_MODE debug mode to show error whene occur
     * * REDIS_HOST host of redis
     * * REDIS_PORT port of redis
     * @return void
     */
    public function __construct($data = [
        'DB_HOST' => null,
        'DB_NAME' => null,
        'DB_USERNAME' => null,
        'DB_PASSWORD' => null,
        'DB_CHARSET' => null,
        'DEBUG_MODE' => null,
        'REDIS_HOST' => null,
        'REDIS_PORT' => null,
    ]){
        $this->host = $data['DB_HOST']?$data['DB_HOST']:$_ENV['DB_HOST'];
        $this->dbname = $data['DB_NAME']?$data['DB_NAME']:$_ENV['DB_NAME'];
        $this->username = $data['DB_USERNAME']?$data['DB_USERNAME']:$_ENV['DB_USERNAME'];
        $this->password = $data['DB_PASSWORD']?$data['DB_PASSWORD']:$_ENV['DB_PASSWORD'];
        $this->charset = $data['DB_CHARSET']?$data['DB_CHARSET']:$_ENV['DB_CHARSET'];
        $this->debug_mode = $data['DEBUG_MODE']?$data['DEBUG_MODE']:$_ENV['DEBUG_MODE'];

        //Redis
        $this->redis = null;
        if(class_exists('Redis')) {
            $this->redis = new Redis();
            $this->redis_host = $data['REDIS_HOST']?$data['REDIS_HOST']:$_ENV['REDIS_HOST'];
            $this->redis_port = $data['REDIS_PORT']?$data['REDIS_PORT']:$_ENV['REDIS_PORT'];
            $this->redis->connect(
                $this->redis_host,
                $this->redis_port
            );
            $this->cached_key = $this->dbname.':';
            //check is redis connected 
            if(!$this->redis->ping()){
                $this->redis = null;
            }
        }
        
        
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
    public function query($sql, $use_redis = false){

        $check_redis = ($this->redis != null && $use_redis);
        if($check_redis){
            $result = $this->getRedisData($sql);
            if(!!$result){
                return $result;
            }
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($check_redis){
            $this->redis->setex($this->getRedisKey($sql), $this->cached_time, serialize($result));
        }
        return $result;
    }

    //function select and prepare
    /**
     * @param string $sql
     * @param array $params
     * @return array|boolean
     */
    public function select($sql, $params = [], $use_redis = false){
        try{

            $check_redis = ($this->redis != null && $use_redis);
            if($check_redis){
                $result = $this->getRedisData($sql, $params);
                if(!!$result){
                    return $result;
                }
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if($check_redis){
                $this->redis->setex($this->getRedisKey($sql, $params), $this->cached_time, serialize($result));
            }
            return $result;
        }catch(PDOException $e){
            if($this->debug_mode){
                return "Connection failed: " . $e->getMessage();
            }
            return $e->getMessage();
        }
    }

    public function setRedisCacheTime($time=60){
        $this->cached_time = $time;
    }

    public function getRedis(){
        return $this->redis;
    }

    public function setRedisData($sql, $params=[], $result){
        if($this->redis) {
            $this->redis->setex($this->getRedisKey($sql, $params), $this->cached_time, serialize($result));
        }
    }

    public function ClearAllRedis(){
        if($this->redis) {
            $this->redis->flushDB();
        }
    }

    /**
     * Get key from sql and params
     * @param string $sql
     * @param array $params
     * @return array|boolean
     */
    private function getRedisKey($sql, $params=[]){
        return $this->cached_key.md5($sql.implode(',', $params));
    }

    /**
     * Get data from redis
     * @param string $sql
     * @param array $params
     * @return array|boolean
     */
    public function getRedisData($sql, $params=[]){
        $cached_key = $this->getRedisKey($sql, $params);
        if($this->redis != null){
            $result = $this->redis->get($cached_key);
            if($result){
                return unserialize($result);
            }
        }
        return false;
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

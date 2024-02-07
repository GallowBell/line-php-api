<?php 

namespace XDark;

class Routes {

    private $req_URL;
    private $req_Method;
    private $pre_URL = null;
    
    function __construct() {
    	$this->req_URL = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->req_Method = $_SERVER['REQUEST_METHOD'];
    }
    
    /**
     * Method GET
     * @param string $url request url endpoint
     * @param callback $cb
     * @param mixed $params parameter pass to callback
     * @return bool
     */
    public function get($url, $cb, $params = []){
    
        if($this->req_Method !== 'GET'){
            return false;
        }

        Self::check_preURL();

        if($this->req_URL !== $url){
            return false;
        }
        
        $result = $cb($params);
        echo $result;
        exit;
    }
    
    /**
     * Method POST
     * @param string $url request url endpoint
     * @param callback $cb callback function
     * @param mixed $params parameter pass to callback
     * @return bool
     */
    public function post($url, $cb, $params = []){
    
        if($this->req_Method !== 'POST'){
            return false;
        }

        Self::check_preURL();
    
        if($this->req_URL !== $url){
            return false;
        }

        $result = $cb($params);
        echo $result;
        exit;
    }

    /**
     * Check pre URL to exclude out if exist
     */
    public function check_preURL(){
        if(isset($this->pre_URL)){
            $this->req_URL = explode($this->pre_URL, explode('?', $_SERVER['REQUEST_URI'])[0])[1];
        }
    }

    /**
     * Set pre URL
     * @param string $pre_URL
     * @return void
     */
    public function set_preURL($pre_URL){
        $this->pre_URL = $pre_URL;
    }

    /**
     * Get pre URL
     * @return string pre_URL
     */
    public function get_preURL(){
        return $this->pre_URL;
    }
}

?>
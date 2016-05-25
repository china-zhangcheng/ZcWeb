<?php

/**
 * Class ApiAuth 接口http请求和授权类
 * @package
 */
class ApiAuth
{
    public $app_key;
    public $app_secret;
    public $access_token;
    public $refresh_token;// 没用上
    public $token_expire_time = 0;

    public $connect_timeout=10;
    public $timeout = 10;
    public $http_code;
    public $http_info;

    public $debug = false;


    //获取access token 刷新token
    function accessTokenURL(){ return NET_NAME.'/api/access_token';}

    //目前授权页先直接返回token
    function authorizeURL(){ return '';}

    //刷新access token 刷新token
    function refreshTokenURL() { return NET_NAME.'/api/refresh_token';}
    /**
     * construct OAuth object
     */
    function __construct($app_key, $app_secret, $access_token = NULL, $refresh_token = NULL) {
        $this->app_key = $app_key;
        $this->app_secret = $app_secret;
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;

        if(!extension_loaded('curl')){
            throw new \Exception('curl extension not loaded!');
        }
    }

    /**
     *
     *
     * @todo 待定
     * @param string $response_type
     *
     * @return string
     */
    function getAuthorizeURL($response_type = 'token')
    {
        $params = array();
        $params['app_key'] = $this->api_key;
        $params['response_type'] = $response_type;

        return $this->authorizeURL().'?'.http_build_query($params);
    }

    /**
     * 获取token
     * @param string $type
     *
     * @return null
     */
    function getAccessToken($type = 'token')
    {
        $params = array();
        $params['app_key'] = $this->app_key;
        $params['app_secret'] = $this->app_secret;
        $params['ip'] = $_SERVER['REMOTE_ADDR'];
        if($this->access_token!=null){
            $params['old_access_token'] = $this->access_token;
        }
        if($type == 'token'){
            $params['grant_type'] = 'refresh_token';
        }else{
            throw new \Exception('wrong auth type');
        }

        $response = $this->OAuthRequest($this->accessTokenURL(),'GET',$params);


        $token = json_decode($response,true);


        if ( is_array($token) && !isset($token['error']) ) {
            
            $this->access_token = $token['access_token'];
            $this->token_expire_time = $token['expire_time'];
        } else {
            throw new \Exception("get access token failed." . $token['error']);
        }
        return $this->access_token;

    }

    function refreshToken()
    {
        $params = array();
        $params['app_key'] = $this->app_key;
        $params['app_secret'] = $this->app_secret;
        $params['ip'] = $_SERVER['REMOTE_ADDR'];
        $params['access_token'] = $this->access_token;
        $response = $this->OAuthRequest($this->refreshTokenURL(),'GET',$params);

        $token = json_decode($response,true);

        if ( is_array($token) && !isset($token['error']) ) {
            $this->access_token = $token['access_token'];
            $this->token_expire_time = $token['expire_time'];
            return $this->access_token;

        } else {
            return false;
        }

    }
    /**
     * 设置请求参数
     *
     * @param $url
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    function OAuthRequest($url,$method,$params,$headers= array(),$debug = false)
    {

        if ( isset($this->access_token) && $this->access_token )
            $params['access_token'] = $this->access_token;

        $method = strtoupper($method);
        switch($method){
            case 'GET':
                $url = $url.'?'.http_build_query($params);
                return $this->http($url,'GET',NULL,$headers,$debug);
            default:
                $body = $params;
                return $this->http($url, $method, http_build_query($body), $headers,$debug);

        }
    }


    function http($url, $method, $postfields = NULL, $headers = array(),$debug = false)
    {
        $this->http_info = array();
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_HEADER, 1);

        $old_cookie = RC()->get('cookie_'.$_SERVER['REMOTE_ADDR']);
        if(!empty($old_cookie)){
            curl_setopt($ci,CURLOPT_COOKIE,$old_cookie);
        }

        switch($method){
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($postfields)) {

                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                }
                break;
            case 'GET':
                break;
        }

        curl_setopt($ci, CURLOPT_URL, $url );
        if(empty($headers)){
            $headers = array();
        }
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

        $response = curl_exec($ci);
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));


        if ($this->debug || $debug) {
            $debug_data = array(
                'debug->request_params'=>$postfields,
                'debug->request_headers'=>$headers,
                'debug->return_info'=>curl_getinfo($ci),
                'debug->return_response'=>$response,
            );

            echo "<pre>";
            var_dump($debug_data);
            echo "</pre>";
        }

        //复用上次的cookie
        preg_match('/Set-Cookie:(.*);/iU',$response,$str);

        if(isset($str[1])){
            RC()->set('cookie_'.$_SERVER['REMOTE_ADDR'],$str[1],3600);
        }
        curl_close ($ci);
        $response = explode("\r\n",$response);
        return array_pop($response);
    }



}
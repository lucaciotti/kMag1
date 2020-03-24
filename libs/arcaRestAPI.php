<?php
include_once($_SERVER['DOCUMENT_ROOT']."/kMag1/config/global_config.php");

// https://www.weichieprojects.com/blog/curl-api-calls-with-php/

class arcaRestAPI {
    private $global_url;

    function __construct(){
        $this->global_url = CONFIG::$SERVER_IP.":".CONFIG::$SERVER_PORT."/api/".CONFIG::$API_VERSION."/";
    }

    public function get($subUrl, $queryUrl){
        $url = $this->global_url.$subUrl;
        if($queryUrl){
            $url = $url.'?'.$queryUrl;
        }
	    // print($url);
        $get_data = $this->callAPI('GET', $url, false);
        return $this->buildResult($get_data);        
    }

    public function post($subUrl, $data_array){
        $url = $this->global_url.$subUrl;
        $make_call = $this->callAPI('POST', $url, $data_array);
        return $this->buildResult($make_call, 'POST');
    }

    public function put($subUrl, $data_array){
        $url = $this->global_url.$subUrl;
        $make_call = $this->callAPI('PUT', $url, $data_array);
        return $this->buildResult($make_call, 'PUT');
    }

    public function delete($subUrl, $data_array){
        $url = $this->global_url . $subUrl;
        $make_call = $this->callAPI('DELETE', $url, $data_array);
        return $this->buildResult($make_call, 'DELETE');
    }


    // PRIVATE FUNCTION
    // ---------------------------------
    private function callAPI($method, $url, $data = false){
        $curl = curl_init();
        $url = str_replace(' ', '%20', $url);
        $header = $this->dataToHeader($data);

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    private function dataToHeader($data=null){
        $header = array();
        array_push($header, "cache-control: no-cache");

        if($data){
            foreach($data as $key => $value)
            {
                array_push($header, $key.": ".$value);
            }
        }

        return $header;
    }

    private function buildResult($response, $method=null){
        $data = json_decode($response, true);
        $result = array(
            'rows' => count($data['success']),
            'data' => $data['success'],
            "error" => $data['errMessage'],
            'json' => $response
        );
        if(empty($result['error']) && ($method=='PUT' || $method=='DELETE') ){
            $result = 'success';
        }
        return $result;
    }
}
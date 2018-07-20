<?php

class BegetHM_api {
    public $endpoint = 'https://api.beget.com/api/';

    /**
     * Get API keys from db and return as part of endpoint
     *
     * @return string
     */
    public function get_keys() {
        return '?login=' . get_option('beget_login') . '&passwd=' . get_option('beget_password');
    }

    /**
     * getAccountInfo
     *
     * @return array
     * @see https://beget.com/ru/api/user#getAccountInfo
     */
    public function getAccountInfo() {        
        $url = $this->endpoint . 'user/getAccountInfo' . $this->get_keys();        
        return $this->curl( $url );
    }

    /**
     * POST request to BegetAPI with CURL
     *
     * @param [string] $url
     * @return array
     * @todo add error handler
     */
    public function curl( $url ) {        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache"            
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        $response = json_decode($response, true);
        return $response['answer']['result'];
    }
}
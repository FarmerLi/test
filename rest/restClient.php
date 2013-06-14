<?php

/**
 * RestClient请求类
 * 
 * $Id: rest_client.php 901 2011-04-15 03:06:59Z wangjm $
 * @copyright Xingchangxinda Inc., all rights reserved.
 * @package webroot
 */
class RestClient
{
    private static $timeout_secondes = 15;

    public function request($url, $method = 'GET', $data = array(), $headers = array(), $auth_user = FALSE, $auth_pwd = FALSE)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::$timeout_secondes);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::$timeout_secondes);
        switch ($method)
        {
            case 'GET':
                curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
                $query = http_build_query($data);
                if (strpos($url, '?') !== FALSE)
                {
                    $url .= '&' . $query;
                }
                else
                {
                    $url .= '?' . $query;
                }
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, TRUE);
                //$headers[] = 'Content-Type: multipart/form-data';
                if (!empty($data))
                {
                    $post_data = http_build_query($data);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
                }
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_PUT, true);
                $file = tmpFile();
                fwrite($file, $data);
                fseek($file, 0);
                curl_setopt($curl, CURLOPT_INFILE, $file);
                curl_setopt($curl, CURLOPT_INFILESIZE, strlen($data));
                break;
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        if (!empty($headers))
        {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        if ($auth_user !== FALSE)
        {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, "{$auth_user}:{$auth_pwd}");
        }
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close ($curl);
        if ($result === FALSE)
        {
            return FALSE;
        }
        $result = $this->parse_response($result);
        $result['http_code'] = $info['http_code'];
        return $result;
    }

    /**
     * 执行rest
     * 
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $headers
     * @param string $auth_user
     * @param string $auth_pwd
     */
    public function execute($url, $method = 'GET', $data = array(), $headers = array(), $auth_user = FALSE, $auth_pwd = FALSE)
    {
        $result = $this->request($url, $method, $data, $headers, $auth_user, $auth_pwd);
        if ($result === FALSE)
        {
            return FALSE;
        }
        $headers = $result['headers'];
        if (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'json') !== FALSE)
        {
            $result['body'] = json_decode($result['body'], TRUE);
        }
        return $result;
    }

    /**
     * 解析回应
     * 
     * @param string $response
     * @return array
     */
    private function parse_response($response)
    {
        $part_pos = strpos($response, "\r\n\r\n");
        $header_part = substr($response, 0, $part_pos);
        $body_part = substr($response, $part_pos + 1);
        $parts  = explode("\r\n", $header_part);
        if (empty($parts))
        {
            return array();
        }
        $length = count($parts);
        $body = $body_part;
        $http_status_line = $parts[0];
        $http_message = 'OK';
        $headers = array();
        $cookies = array();
        for ($i = 1; $i < $length; $i++)
        {
            $pos = strpos($parts[$i], ':');
            if ($pos !== FALSE)
            {
                $key = substr($parts[$i], 0, $pos);
                $val = substr($parts[$i], $pos + 2);
                if (strtoupper($key) == 'SET-COOKIE')
                {
                    $cookies[] = $val;
                    continue;
                }
                $headers[$key] = $val;
            }
        }
        return array('headers' => $headers, 'body' => $body, 'http_message' => $http_message, 'response' => $response);
    }
}
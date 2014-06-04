<?php
namespace Resttest;

use Resttest\Response,
    Resttest\JsonResponse;

/**
 * rest client
 *
 * @author Farmer.Li <me@farmerli.com>
 */
class RestClient
{
    const USER_AGENT = 'CAT PHP RestClient';

    private $_params = [];

    private $_curl;

    private $_saveCookie = false;

    private $_sendCookie = false;

    private $_cookiePath = '';

    /**
     * 构造
     *
     * @return void
     */
    private function __construct()
    {
        $this->_curl = curl_init();
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($this->_curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->_curl, CURLOPT_HEADER, true); 
    }

    /**
     * 执行
     * 
     * @return array
     */
    public function execute()
    {
        if ($this->_method === "POST") {
            curl_setopt($this->_curl, CURLOPT_POST, true);
            curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $this->_params);
            curl_setopt(
                $this->_curl, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data")
            );

            if (! is_array($this->_params)) {
                throw new \Exception("[RestClient] Post params must is an array");
            }
        } else if ($this->_method == "GET") {
            curl_setopt($this->_curl, CURLOPT_HTTPGET, true);
            $this->_treatURL();
        } else if ($this->_method === "PUT") {
            curl_setopt($this->_curl, CURLOPT_PUT, true);
            $this->_treatURL();
            $this->_file = tmpFile();
            fwrite($this->_file, $this->_params);
            fseek($this->_file, 0);
            curl_setopt($this->_curl, CURLOPT_INFILE, $this->_file);
            curl_setopt($this->_curl, CURLOPT_INFILESIZE, strlen($this->_params));
        } else {
            curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, $this->_method);
            $this->_treatURL();
        }

        if ($this->_saveCookie) {
            curl_setopt($this->_curl, CURLOPT_COOKIEJAR, $this->_cookiePath);
        }

        if ($this->_sendCookie) {
            curl_setopt($this->_curl, CURLOPT_COOKIEFILE, $this->_cookiePath);
        }

        if ($this->_isSecureHttp()) {
            throw new \Exception('not support HTTPS!');
        }
        curl_setopt($this->_curl, CURLOPT_URL, $this->_url);
        curl_setopt($this->_curl, CURLOPT_USERAGENT, self::USER_AGENT);

        $r = curl_exec($this->_curl);
        if (false === $r) {
            throw new \Exception(
                sprintf(
                    'request failed, url = "%s", error = "%s"', 
                    $this->_url, curl_error($this->_curl)
                )
            );
        }

        return $this->_treatResponse($r); // Extract the headers and response
    }

    public function sendCookie($path)
    {
        $this->_sendCookie = true;
        $this->_cookiePath = $path;
        return $this;
    }

    public function saveCookie($path)
    {
        $this->_saveCookie = true;
        $this->_cookiePath = $path;
        return $this;
    }

    /**
     * 处理 URL
     * 
     * @return string
     */
    private function _treatURL()
    {
        // Transform parameters in key/value pars in URL
        if (is_array($this->_params) && count($this->_params) >= 1) {
            if (!strpos($this->_url, '?')) {
                $this->_url .= '?' ;
            }
            foreach ($this->_params as $k => $v) {
                $this->_url .= "&".urlencode($k)."=".urlencode($v);
            }
        }
        return $this->_url;
    }

    private function _treatResponse($r)
    {
        if ($r == null or strlen($r) < 1) {
            return;
        }

        // HTTP packets define that Headers end in a blank line (\n\r) where starts the body
        $parts  = explode("\r\n\r\n", $r);

        while (preg_match('@HTTP/1.[0-1] 100 Continue@', $parts[0])
            or preg_match("@Moved@", $parts[0])
            ) {
            // Continue header must be bypass
            for ($i = 1;$i < count($parts); $i++) {
                $parts[$i - 1] = trim($parts[$i]);
            }
            unset($parts[count($parts) - 1]);
        }

        $headerPattern = "@(.+?): ([^\r|\n]*)@";

        if (preg_match_all($headerPattern, $parts[0], $headers)) {

            if (isset($headers[1]) && isset($headers[2])) {
                
                $headerValues = $headers[2];

                foreach ($headers[1] as $key => $headerName) {
                    $this->_headers[$headerName] = $headerValues[$key];

                }

            }

        }

        // This extracts the response header Code and Message
        preg_match("@HTTP/1.[0-1] ([0-9]{3}) (.+)@", $parts[0], $reg);
        if (isset($reg[1])) {
            $this->_headers['Code'] = $reg[1];
        }

        if (isset($reg[2])) {
            $this->_headers['Message'] = $reg[2];
        }

        $this->_response = "";

        //This make sure that exploded response get back togheter
        for ($i = 1;$i < count($parts); $i++) {
            if ($i > 1) {
                $this->_response .= "\n\r";
            }
            $this->_response .= $parts[$i];
        }
        return [
            'header' => $this->_headers,
            'body' => $this->_response
        ];
    }

    /**
     * 检查当前 URL 是否为 HTTPS
     *
     * @return boolean
     */
    private function _isSecureHttp()
    {
        $info = parse_url($this->_url);
        return $info['scheme'] === 'https';
    }

    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * 单例
     * 
     * @return RestClient
     */
    public function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new RestClient();
        }
        return $instance;
    }

    public function addFile($key, $file)
    {
        if ($this->_method !== 'POST') {
            throw new \Exception('upload file must use POST request method!');
        }

    }

    public function addParam($key, $value)
    {
        $this->_params[$key] = $value;
    }
}
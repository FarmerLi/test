<?php
/**
 * response
 *
 * @author Farmer.Li <me@farmerli.com>
 */
namespace Resttest\Response;

/**
 * response
 */
class Response
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getCode()
    {
        return $this->data['header']['Code'];
    }

    public function getMessage()
    {
        return $this->data['header']['Message'];
    }

    public function getContentType()
    {
        return $this->data['header']['Content-Type'];
    }

    public function getBody()
    {
        return $this->data['body'];
    }

    public function getCookie()
    {
        return $this->data['header']['Set-Cookie'];
    }
}
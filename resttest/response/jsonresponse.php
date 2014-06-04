<?php
/**
 * json Response
 *
 * @author Farmer.Li <me@farmerli.com>
 */
namespace Resttest\Response;

use Resttest\Response\Response;

/**
 * json Response
 */
class JsonResponse extends Response
{
    /**
     * return json decode
     * 
     * @return array
     */
    public function getBody()
    {
        $body = parent::getBody();
        try {
            $data = json_decode($body, true);
        } catch (Exception $e) {

        }
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception ('response body not json data : ' . $body);
        }
        return $data;
    }
}
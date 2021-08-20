<?php

namespace Idrac\WsMan;

use Exception;

/**
 * Idrac WsMan Client to send queries and commands to the idrac WsMan interface
 */
class Client
{

    /** @var string */
    private $url;
    /** @var string */
    private $user;
    /** @var string */
    private $password;

    /**
     * @param string $url
     * @param string $user
     * @param string $password
     */
    public function __construct($url, $user, $password)
    {
        $this->url = $url;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Performs a request
     *
     * @param Command $command
     * @throws Exception
     * @return JobResponse
     */
    public function perform(Command $command)
    {
        $command->setEndpoint($this->url);
        $response = $this->send($command->getAsXML());

        return new JobResponse($response);
    }

    /**
     * Performs a query request
     *
     * @param DataQuery $query
     * @throws Exception
     * @return DataQueryResponse
     */
    public function query(DataQuery $query)
    {
        $query->setEndpoint($this->url);
        $response = $this->send($query->getAsXML());
        $className = $this->getResponseClassNameForQuery($query);

        if (class_exists($className)) {
            return new $className($response);
        }

        return new DataQueryResponse($response);
    }

    /**
     * Send this bunch of xml data to the client and return the xml answer
     *
     * @param string $xml
     * @throws Exception
     * @return string
     */
    private function send($xml)
    {
        $context = $this->getStreamContext($xml);

        // epic retry
        $response = "";
        for ($i=0;$i<5;$i++) {
            $response = @file_get_contents($this->url, false, $context);

            if ($response !== false) {
                break;
            }
            sleep(1);
        }

        if ($response === false) {
            throw new Exception("Connection to [url={$this->url}] failed");
        }

        return $response;
    }

    /**
     * Constructs a stream context for a http POST call with the given content
     *
     * @param string $content
     * @return resource A stream context resource.
     */
    private function getStreamContext($content)
    {
        $basicAuth = base64_encode("{$this->user}:{$this->password}");
        $contextOptions = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/xml\r\n" .
                            "Authorization: Basic {$basicAuth}\r\n",
                'content' => $content,
                'timeout' => 60,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'ciphers' => 'DEFAULT:!DH',
            ]
        ];
        return stream_context_create($contextOptions);
    }

    /**
     * Get response class name for the given query class
     *
     * @param DataQuery $query
     * @return string
     */
    private function getResponseClassNameForQuery(DataQuery $query)
    {
        $className = get_class($query);

        return str_replace('Query', 'Response', $className);
    }

    /**
     * Returns the wsman url for the given host
     *
     * @param string $hostname
     * @return string
     */
    public static function getUrl($hostname)
    {
        return "https://{$hostname}/wsman";
    }
}

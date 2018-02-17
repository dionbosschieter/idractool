<?php

namespace Idrac\WsMan;

use Exception;

/**
 * Response object that gets returned by the wsman client on a generic DataQuery
 */
class DataQueryResponse extends Response
{

    /**
     * Returns the value of a tag in the response
     *
     * @param string $tagName
     * @return string
     * @throws Exception when tag does not exists
     */
    public function getValueOfTagName($tagName)
    {
        $nodeList = $this->dom->getElementsByTagName($tagName);

        if ($nodeList->length === 0) {
            throw new Exception("TagName[name={$tagName}] does not exists");
        }

        return $nodeList->item(0)->nodeValue;
    }
}

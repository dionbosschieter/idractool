<?php

namespace Idrac\WsMan;

/**
 * Custom response for the NicInventoryQuery
 */
class NicEnumerationResponse extends Response
{

    /**
     * Get the Nic objects for this response
     *
     * @return NicConfig[]
     */
    public function getNicConfigs(): array
    {
        $items = $this->xpath->query('.//wsman:Items');
        $childNodes = $items->item(0)->childNodes;

        $allData = [];
        foreach ($childNodes as $node) {
            $nodeData = [];
            foreach ($node->childNodes as $childNode) {
                list(,$keyName) = explode(':', $childNode->tagName);

                $nodeData[$keyName] = $childNode->nodeValue;
            }

            $fqdd = $nodeData['FQDD'];
            $allData[$fqdd][] = [
                'name'     => $nodeData['AttributeName'],
                'value'    => $nodeData['CurrentValue'],
                'readOnly' => $nodeData['IsReadOnly'],
                'pending'  => $nodeData['PendingValue'],
            ];
        }


        $configs = [];
        foreach ($allData as $nic => $config) {
            $configs[] = new NicConfig($nic, $config);
        }

        return $configs;
    }
}

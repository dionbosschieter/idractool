<?php

namespace Idrac;

use Ensure;
use ErrorCodes;
use SimpleXMLElement;

/**
 * Value object for a dell firmware software component
 */
class Firmware
{

    /** @var SimpleXMLElement */
    private $model;

    /** @var array */
    private $keysThatShouldExist = ['vendorVersion', 'path', 'packageID', 'packageType', 'hashMD5'];

    /**
     * Takes as input a softwarecomponent node as SimpleXMLElement from the catalog xml file
     *
     * @param SimpleXMLElement $firmwareModel
     * @throws Exception when expected firmware model keys are empty or nonexistent
     */
    public function __construct(SimpleXMLElement $firmwareModel)
    {
        foreach ($this->keysThatShouldExist as $keyThatShouldExist) {
            if ( ! isset($firmwareModel[$keyThatShouldExist])) {
                throw new Exception("The given firmware model does not contain key '{$keyThatShouldExist}'", ErrorCodes::INTERNAL);
            }

            Ensure::notEmpty($firmwareModel[$keyThatShouldExist], "The firmware model's key '{$keyThatShouldExist}' is empty");
        }

        $this->model = $firmwareModel;
    }

    /**
     * Constructs and returns a list of firmwares foreach given firmware model
     *
     * @param array $list
     * @return Firmware[]
     */
    public static function collection(array $list)
    {
        return array_map(function ($model) {
            return new static($model);
        }, $list);
    }

    /**
     * Returns version string of the firmware software component
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->model['vendorVersion'];
    }

    /**
     * Returns download path of the firmware software component
     *
     * @return string
     */
    public function getPath()
    {
        return $this->model['path'];
    }

    /**
     * Returns package id of the firmware software component
     *
     * @return string
     */
    public function getPackageId()
    {
        return $this->model['packageID'];
    }

    /**
     * Returns package type of the firmware software component
     *
     * @return string
     */
    public function getPackageType()
    {
        return $this->model['packageType'];
    }

    /**
     * Returns the software firmware file's md5 hash
     *
     * @return string
     */
    public function getMD5Hash()
    {
        return strtolower($this->model['hashMD5']);
    }

    /**
     * Return the filename of the firmware installation file
     *
     * @return string
     */
    public function getFileName()
    {
        return basename($this->getPath());
    }

    /**
     * Returns the full download path of the firmware
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return "https://downloads.dell.com/{$this->getPath()}";
    }
}

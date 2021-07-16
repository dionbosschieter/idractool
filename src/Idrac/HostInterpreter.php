<?php

namespace Idrac;

class HostInterpreter
{
    /**
     * @var string
     */
    private $hostArgument;

    public function __construct(string $hostArgument)
    {
        $this->hostArgument = $hostArgument;
    }

    public function getAllHosts(): array
    {
        // hosts is a file, read and return it
        if (file_exists($this->hostArgument)) {
            return array_map('trim', file($this->hostArgument));
        }

        // parse the ansible like host argument
        if ($this->isAnsibleLikeArgument()) {
            return $this->interpretAnsibleHostArgument();
        }

        // there is only one host
        return [$this->hostArgument];
    }

    private function isAnsibleLikeArgument(): bool
    {
        return preg_match('/\[([0-9]+:[0-9]+)\]/', $this->hostArgument);
    }

    private function interpretAnsibleHostArgument(): array
    {
        preg_match_all('/\[([0-9]+:[0-9]+)\]/', $this->hostArgument, $matches);
        $quantifiers = $matches[1];

        $returnHostnames = [''];
        $hostnameParts = preg_split('/\[([0-9]+:[0-9]+)\]/', $this->hostArgument);
        foreach ($hostnameParts as $index => $hostnamePart) {
            $quantifier = $quantifiers[$index] ?? ':';
            list($start, $end) = explode(':', $quantifier);

            $newList = [];
            for ($i=$start; $i <= $end; $i++) {
                foreach ($returnHostnames as $hostname) {
                    $newHostname = $hostname . $hostnamePart . $i;
                    $newList[] = $newHostname;
                }
            }
            $returnHostnames = $newList;
        }

        return $returnHostnames;
    }
}
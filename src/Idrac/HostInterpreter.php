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
        if (file_exists($this->hostArgument)) {
            return array_map('trim', file($this->hostArgument));
        }

        if ($this->isAnsibleLikeArgument()) {
            return $this->interpretAnsibleHostArgument();
        }

        return [$this->hostArgument];
    }

    // todo: change match to a proper regex
    private function isAnsibleLikeArgument(): bool
    {
        return strpos('[', $this->hostArgument) !== false;
    }

    private function interpretAnsibleHostArgument(): array
    {
        preg_match('/(.*)\([0-9+:0-9+\])(.*)/', $this->hostArgument, $matches);
        foreach ($matches as $match) {

        }
    }
}
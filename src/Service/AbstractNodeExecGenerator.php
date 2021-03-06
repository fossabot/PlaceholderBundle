<?php

namespace BernhardWebstudio\PlaceholderBundle\Service;

use BernhardWebstudio\PlaceholderBundle\Service\PlaceholderGeneratorInterface;

/**
 * Abstract Generator for all NodeJS related placeholders
 */
abstract class AbstractNodeExecGenerator implements PlaceholderGeneratorInterface
{
    abstract public function __construct($bin, $node_bin, $iterations);

    public function getOutputExtension()
    {
        return '.svg';
    }

    public function getOutputMime()
    {
        return 'image/svg+xml';
    }
}

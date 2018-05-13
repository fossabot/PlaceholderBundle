<?php

namespace BernhardWebstudio\PlaceholderBundle\Service;

use BernhardWebstudio\PlaceholderBundle\Service\PlaceholderGeneratorInterface;
use Psr\Log\LoggerInterface;

class PlaceholderProviderService
{
    protected $generator;
    protected $logger;
    protected $loadPaths;

    const MODE_RAW = 'raw';
    const MODE_BASE_64 = 'base64';
    const MODE_PATH = 'path';

    public function __construct(
        PlaceholderGeneratorInterface $generator,
        array $loadPaths = array(),
        LoggerInterface $logger = null
    ) {
        $this->generator = $generator;
        $this->loadPaths = $loadPaths;
        $this->logger = $logger;
    }

    public function getPlaceholder($inputfile, $mode = '')
    {
        // resolve input path
        $inputfile = $this->getInputPath($inputfile);
        if (!$inputfile) {
            return;
        }
        $outputfile = $this->getOutputPath($inputfile);
        if (!\file_exists($outputfile) || filemtime($inputfile) > filemtime($outputfile)) {
            $this->generator->generate($inputfile, $outputfile);
        }

        switch ($mode) {
            case self::MODE_BASE_64:
                return \base64_encode(\file_get_contents($outputfile));
                break;
            // alternative: serve the path to the controller instead.
            // This way, the time used to serve can be reduced
            case self::MODE_RAW:
                return \file_get_contents($outputfile);
                break;
            case self::MODE_PATH:
            default:
                return $outputfile;
                break;
        }
    }

    /**
     * Get the actual path to a generated placeholder
     */
    public function getOutputPath(string $filename)
    {
        $extension_pos = strrpos($filename, '.'); // find position of the last dot, so where the extension starts
        $thumb = substr($filename, 0, $extension_pos) . '_thumb' . substr($filename, $extension_pos);
        // let the service add a custom extension
        return $thumb . $this->generator->getOutputExtension();
    }

    public function getOutputMime()
    {
        return $this->generator->getOutputMime();
    }

    /**
     * Get the actual path to an image
     */
    public function getInputPath(string $filename)
    {
        // test out the possible paths
        $index = 0;
        $testPath = $filename;
        while (!\file_exists($testPath) && $index < count($this->loadPaths)) {
            $testPath = $this->loadPaths[$index] . $filename;
            $index++;
        }
        return \file_exists($testPath) ? $testPath : null;
    }
}

<?php

/*
 * This file is part of PhpSpec, A php toolset to drive emergent
 * design by specification.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpSpec\Loader;

class StreamWrapper
{
    private $realPath;
    private $fileResource;

    private static $specTransformers = array();

    public static function register()
    {
        if (in_array('phpspec', stream_get_wrappers())) {
            stream_wrapper_unregister('phpspec');
        }
        stream_wrapper_register('phpspec', 'PhpSpec\Loader\StreamWrapper');
    }

    public static function reset()
    {
        static::$specTransformers = array();
    }

    public static function addTransformer(SpecTransformer $specTransformer)
    {
        static::$specTransformers[] = $specTransformer;
    }

    public static function wrapPath($path)
    {
        if (!defined('HHVM_VERSION'))
        {
            return 'phpspec://' . $path;
        }

        return $path;
    }

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        if ($mode != 'rb') {
            throw new \RuntimeException('Cannot open phpspec url in mode "$mode"');
        }

        $this->realPath = preg_replace('|^phpspec://|', '', $path);

        if (!file_exists($this->realPath)) {
            return false;
        }

        $content = file_get_contents($this->realPath);

        foreach (static::$specTransformers as $specTransformer) {
            $content = $specTransformer->transform($content);
        }

        $this->fileResource = fopen('php://memory', 'w+');
        fwrite($this->fileResource, $content);
        rewind($this->fileResource);

        $opened_path = $this->realPath;
        return true;
    }

    public function stream_stat()
    {
        return stat($this->realPath);
    }

    public function stream_read($count)
    {
        return fread($this->fileResource, $count);
    }

    public function stream_eof()
    {
        return feof($this->fileResource);
    }
}

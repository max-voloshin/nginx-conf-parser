<?php

namespace MaxVoloshin\NginxConfParser;

class Filesystem
{
    public function maskToPaths($input)
    {
        return glob($input);
    }

    public function read($path)
    {
        return file_get_contents($path);
    }
}

<?php

namespace MaxVoloshin\NginxConfParser;

class Command
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function run(array $arguments)
    {
        if (count($arguments) > 2) {

            $text = "Too much arguments\n";
            $code = 1;

        } elseif (!empty($arguments[1])) {

            $parser = new Parser($this->filesystem);
            $text = $parser->run($this->filesystem->read($arguments[1]));
            $code = 0;

        } else {

            $readme = $this->filesystem->read(__DIR__ . '/../README.MD');
            $readme = str_replace('``', '', $readme);
            $text = <<<TEXT
Nginx conf parser
~~~~~~~~~~~~~~~~~

{$readme}

TEXT;
            $code = 0;

        }

        return array($code, $text);

    }

}

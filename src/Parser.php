<?php

namespace MaxVoloshin\NginxConfParser;

class Parser
{
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function run($input)
    {
        $lines = explode(PHP_EOL, $input);

        foreach ($lines as $index => $line) {

            if (preg_match('/^\\s*include\\s+([^\\s]+)\\s*;/', $line, $matches)) {

                $lines[$index] = '';

                foreach ($this->filesystem->maskToPaths($matches[1]) as $path) {

                    $lines[$index] .= $this->run($this->filesystem->read($path));

                }

                $lines[$index] = rtrim($lines[$index], PHP_EOL);

            }

        }

        return implode(PHP_EOL, $lines);
    }

}

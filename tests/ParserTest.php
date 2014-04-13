<?php

namespace MaxVoloshin\NginxConfParser;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    public function testRaw()
    {
        $parser = new Parser($this->getMock('\MaxVoloshin\NginxConfParser\Filesystem'));
        $this->assertSame(
            "worker_processes  1;\n",
            $parser->run("worker_processes  1;\n")
        );
    }

    public function testInclude()
    {
        $filesystem = $this->getMock('\MaxVoloshin\NginxConfParser\Filesystem');

        $filesystem
            ->expects($this->atLeastOnce())
            ->method('read')
            ->with('some.conf')
            ->will($this->returnValue("pid        logs/nginx.pid;\n"));

        $filesystem
            ->expects($this->atLeastOnce())
            ->method('maskToPaths')
            ->will($this->returnCallback(function ($x) {return array($x);}));

        $content = <<<TEXT
worker_processes  1;
include some.conf;
include  some.conf  ;
 include some.conf;
#  include some.conf;
TEXT;

        $expected = <<<TEXT
worker_processes  1;
pid        logs/nginx.pid;
pid        logs/nginx.pid;
pid        logs/nginx.pid;
#  include some.conf;
TEXT;

        $parser = new Parser($filesystem);

        $this->assertSame($expected, $parser->run($content));
    }

    public function testRecursiveInclude()
    {
        $filesystem = $this->getMock('\MaxVoloshin\NginxConfParser\Filesystem');

        $filesystem
            ->expects($this->atLeastOnce())
            ->method('read')
            ->will(
                $this->returnValueMap(
                    array(
                        array('first.conf', "first;\ninclude second.conf;\n"),
                        array('second.conf', "second;\ninclude third.conf;\n"),
                        array('third.conf', "third;\n")
                    )
                )
            );

        $filesystem
            ->expects($this->atLeastOnce())
            ->method('maskToPaths')
            ->will($this->returnCallback(function ($x) {return array($x);}));

        $content = "zero;\ninclude first.conf;\n";
        $expected = "zero;\nfirst;\nsecond;\nthird;\n";

        $parser = new Parser($filesystem);

        $this->assertSame($expected, $parser->run($content));
    }

    public function testMaskInclude()
    {
        $filesystem = $this->getMock('\MaxVoloshin\NginxConfParser\Filesystem');

        $filesystem
            ->expects($this->atLeastOnce())
            ->method('read')
            ->will(
                $this->returnValueMap(
                    array(
                        array('first.conf', "first;\n"),
                        array('second.conf', "second;\n"),
                        array('third.conf', "third;\n")
                    )
                )
            );

        $filesystem
            ->expects($this->atLeastOnce())
            ->method('maskToPaths')
            ->with('*.conf')
            ->will($this->returnValue(array('first.conf', 'second.conf', 'third.conf')));

        $content = "zero;\ninclude *.conf;\n";
        $expected = "zero;\nfirst;\nsecond;\nthird;\n";

        $parser = new Parser($filesystem);

        $this->assertSame($expected, $parser->run($content));
    }
}

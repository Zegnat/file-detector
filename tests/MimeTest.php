<?php

declare(strict_types=1);

namespace Test;

use \Ancarda\File\Detector;
use \PHPUnit\Framework\TestCase;

final class MimeTest extends TestCase
{
    private $detector;

    public function setUp()
    {
        $this->detector = new \Ancarda\File\Detector;
    }

    public function testText()
    {
        $file = $this->fileFromString('this is some text');
        $mime = $this->detector->determineMimeType($file);
        $this->assertEquals('text/plain; charset=utf-8', $mime);
    }

    public function testJpeg()
    {
        $this->assertFileHasMimeType('sample.jpg', 'image/jpg');
    }

    public function testPng()
    {
        $this->assertFileHasMimeType('sample.png', 'image/png');
    }

    public function testGif()
    {
        $this->assertFileHasMimeType('sample-87a.gif', 'image/gif');
        $this->assertFileHasMimeType('sample-89a.gif', 'image/gif');
    }

    public function testXml()
    {
        $this->assertFileHasMimeType('sample.xml', 'text/xml');
    }

    public function testWebp()
    {
        $this->assertFileHasMimeType('sample.webp', 'image/webp');
    }

    public function testDetermineDimensions()
    {
        $file = new \SplFileObject(__DIR__ . '/files/sample.png', 'r');

        $this->assertSame([16, 16], $this->detector->determineDimensions($file));
    }

    public function testDetermineDimensionsWithRectangleImage()
    {
        $file = new \SplFileObject(__DIR__ . '/files/sample2.jpg', 'r');

        $this->assertSame([3, 7], $this->detector->determineDimensions($file));
    }

    public function testDetermineDimensionsWithInvalidFileType()
    {
        $file = new \SplFileObject(__DIR__ . '/files/sample.xml', 'r');

        $this->expectException(\InvalidArgumentException::class);

        $this->detector->determineDimensions($file);
    }

    public function testDetermineDimensionsWithNonZeroPosition()
    {
        $file = new \SplFileObject(__DIR__ . '/files/sample.jpg', 'r');
        $file->ftell();
        $file->fread(512);

        $this->assertEquals([16, 16], $this->detector->determineDimensions($file));
    }

    public function testFlac()
    {
        $this->assertFileHasMimeType('sample.flac', 'audio/flac');
    }

    private function assertFileHasMimeType(string $path, string $filetype)
    {
        $file = new \SplFileObject(__DIR__ . '/files/' . $path, 'r');
        $mime = $this->detector->determineMimeType($file);
        $this->assertEquals($filetype, $mime);
    }

    private function fileFromString(string $s)
    {
        $file = new \SplTempFileObject();
        $file->fwrite($s);
        $file->rewind();
        return $file;
    }
}

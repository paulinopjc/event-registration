<?php

namespace Tests\Controllers;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\Output\QRGdImagePNG;
use CodeIgniter\Test\CIUnitTestCase;

class QrCodeTest extends CIUnitTestCase
{
    public function testQrCodeRendersAsDataUri()
    {
        $qrDataUri = (new QRCode([
            'outputInterface' => QRGdImagePNG::class,
            'scale' => 10,
        ]))->render('EVT-TEST01');

        $this->assertStringStartsWith('data:image/png;base64,', $qrDataUri);
    }

    public function testQrCodeBase64DecodesToValidPng()
    {
        $qrDataUri = (new QRCode([
            'outputInterface' => QRGdImagePNG::class,
            'scale' => 10,
        ]))->render('EVT-TEST01');

        $qrBase64 = explode(',', $qrDataUri, 2)[1];
        $rawPng = base64_decode($qrBase64);

        // PNG files start with an 8-byte signature
        $pngSignature = "\x89PNG\r\n\x1a\n";
        $this->assertStringStartsWith($pngSignature, $rawPng);
    }

    public function testQrCodeSavesToValidPngFile()
    {
        $qrDataUri = (new QRCode([
            'outputInterface' => QRGdImagePNG::class,
            'scale' => 10,
        ]))->render('EVT-TEST01');

        $qrBase64 = explode(',', $qrDataUri, 2)[1];
        $rawPng = base64_decode($qrBase64);

        $tmpPath = WRITEPATH . 'qrcodes/test-qr.png';
        @mkdir(dirname($tmpPath), 0755, true);
        file_put_contents($tmpPath, $rawPng);

        // Verify file exists and is a valid image
        $this->assertFileExists($tmpPath);
        $imageInfo = getimagesize($tmpPath);
        $this->assertNotFalse($imageInfo);
        $this->assertEquals(IMAGETYPE_PNG, $imageInfo[2]);
        $this->assertGreaterThan(0, $imageInfo[0]); // width
        $this->assertGreaterThan(0, $imageInfo[1]); // height

        @unlink($tmpPath);
    }

    public function testQrCodeBase64IsValidForApiAttachment()
    {
        $qrDataUri = (new QRCode([
            'outputInterface' => QRGdImagePNG::class,
            'scale' => 10,
        ]))->render('EVT-TEST01');

        $qrBase64 = explode(',', $qrDataUri, 2)[1];

        // Verify base64 string is valid (no data URI prefix, no whitespace issues)
        $this->assertNotEmpty($qrBase64);
        $this->assertDoesNotMatchRegularExpression('/^data:/', $qrBase64);
        $this->assertEquals($qrBase64, base64_encode(base64_decode($qrBase64)));
    }
}

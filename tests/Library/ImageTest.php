<?php

declare(strict_types=1);

use Opencart\System\Library\Image;
use PHPUnit\Framework\TestCase;

final class ImageTest extends TestCase {
	protected function setUp(): void {
		if (!extension_loaded('gd')) {
			$this->markTestSkipped('GD extension required');
		}
	}

	private function makePng(int $w, int $h): string {
		$file = sys_get_temp_dir() . '/oc_img_' . uniqid('', true) . '.png';
		$im = imagecreatetruecolor($w, $h);
		imagepng($im, $file);
		imagedestroy($im);

		return $file;
	}

	public function testLoadResizeCropRotate(): void {
		$file = $this->makePng(20, 10);
		$image = new Image($file);
		$this->assertSame(20, $image->getWidth());
		$this->assertSame(10, $image->getHeight());
		$this->assertSame('image/png', $image->getMime());

		$image->resize(10, 10);
		$this->assertSame(10, $image->getWidth());
		$image->crop(0, 0, 4, 4);
		$this->assertSame(4, $image->getWidth());
		$image->rotate(90);
		$this->assertSame(4, $image->getWidth());
		$this->assertSame(4, $image->getHeight());
	}

	public function testWatermark(): void {
		$base = $this->makePng(40, 40);
		$mark = $this->makePng(5, 5);
		$image = new Image($base);
		$watermark = new Image($mark);
		$image->watermark($watermark, 'bottomright');
		$this->assertSame(40, $image->getWidth());
	}

	public function testNonImageFileThrows(): void {
		$text = sys_get_temp_dir() . '/oc_notimg_' . uniqid('', true) . '.png';
		file_put_contents($text, 'not an image');
		$this->expectException(Exception::class);
		new Image($text);
	}

	public function testMissingFileThrows(): void {
		$this->expectException(Exception::class);
		new Image('/no/such/file.png');
	}

	public function testSaveWithoutExtension(): void {
		$file = $this->makePng(2, 2);
		$image = new Image($file);
		$out = sys_get_temp_dir() . '/oc_img_out_' . uniqid('', true);
		$image->save($out);
		$this->assertFileDoesNotExist($out);
	}
}

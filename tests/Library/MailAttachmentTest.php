<?php

declare(strict_types=1);

use Opencart\System\Library\Mail;
use Opencart\System\Library\Mail\Mail as MailAdaptor;
use Opencart\System\Library\Mail\Smtp as SmtpAdaptor;
use PHPUnit\Framework\TestCase;

final class MailAttachmentTest extends TestCase {
	private function attachmentsProperty(object $object): array {
		$ref = new ReflectionClass($object);
		$prop = $ref->getProperty('attachments');
		$prop->setAccessible(true);

		return $prop->getValue($object);
	}

	public function testFacadeDelegatesToMailAdaptor(): void {
		$path = sys_get_temp_dir() . '/attach.txt';
		file_put_contents($path, 'x');
		$mail = new Mail('mail');
		$mail->addAttachment($path);
		$ref = new ReflectionClass($mail);
		$adaptorProp = $ref->getProperty('adaptor');
		$adaptorProp->setAccessible(true);
		$adaptor = $adaptorProp->getValue($mail);
		$this->assertSame([$path], $this->attachmentsProperty($adaptor));
	}

	public function testAdaptorsStoreAttachment(): void {
		$path = sys_get_temp_dir() . '/attach2.txt';
		file_put_contents($path, 'y');
		$mail = new MailAdaptor();
		$mail->addAttachment($path);
		$this->assertSame([$path], $this->attachmentsProperty($mail));

		$smtp = new SmtpAdaptor();
		$smtp->addAttachment($path);
		$this->assertSame([$path], $this->attachmentsProperty($smtp));
	}
}

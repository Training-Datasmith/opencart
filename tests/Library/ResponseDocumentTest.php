<?php

declare(strict_types=1);

use Opencart\System\Library\Document;
use Opencart\System\Library\Response;
use PHPUnit\Framework\TestCase;

final class ResponseDocumentTest extends TestCase {
	public function testResponseHeadersAndOutput(): void {
		$response = new Response();
		$response->addHeader('X-Test: one');
		$response->addHeader('X-Test: two');
		$response->setOutput('body');
		$this->assertSame(['X-Test: one', 'X-Test: two'], $response->getHeaders());
		$this->assertSame('body', $response->getOutput());
	}

	public function testDocumentMetadata(): void {
		$document = new Document();
		$document->setTitle('T');
		$document->setDescription('D');
		$document->setKeywords('K');
		$document->addLink('/a.css', 'stylesheet');
		$document->addStyle('/b.css', 'stylesheet', 'print');
		$document->addScript('/c.js');
		$document->addMeta(['name' => 'description', 'content' => 'Page']);
		$this->assertSame('T', $document->getTitle());
		$this->assertSame('D', $document->getDescription());
		$this->assertSame('K', $document->getKeywords());
		$this->assertArrayHasKey('/a.css', $document->getLinks());
		$this->assertArrayHasKey('/b.css', $document->getStyles());
		$this->assertArrayHasKey('/c.js', $document->getScripts());
		$this->assertSame([['name' => 'description', 'content' => 'Page']], $document->getMetas());
	}
}

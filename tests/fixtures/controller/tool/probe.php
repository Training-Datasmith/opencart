<?php

namespace Opencart\Test\Controller\Tool;

class Probe extends \Opencart\System\Engine\Controller {
	public function index(string &$markerFile): void {
		file_put_contents($markerFile, 'index', FILE_APPEND);
	}

	public function first(string &$markerFile): void {
		file_put_contents($markerFile, 'first', FILE_APPEND);
	}

	public function second(string &$markerFile): void {
		file_put_contents($markerFile, 'second', FILE_APPEND);
	}
}

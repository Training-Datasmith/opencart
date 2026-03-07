<?php

namespace Tools\PHPStan;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\NeverType;
use PHPStan\Type\Type;

class LoadedProperty implements PropertyReflection {
	public function __construct(private ClassReflection $declaringClass, private Type $type, bool $isWritable = false) {
		$this->isWritable = $isWritable;
	}

	public function getDeclaringClass(): ClassReflection {
		return $this->declaringClass;
	}

	public function isStatic(): bool {
		return false;
	}

	public function isPrivate(): bool {
		return false;
	}

	public function isPublic(): bool {
		return true;
	}

	public function isReadable(): bool {
		return true;
	}

	public function isWritable(): bool {
		return $this->isWritable;
	}

	public function getDocComment(): ?string {
		return null;
	}

	public function getReadableType(): Type {
		return $this->type;
	}

	public function getWritableType(): Type {
		return new NeverType();
	}

	public function canChangeTypeAfterAssignment(): bool {
		return false;
	}

	public function isDeprecated(): TrinaryLogic {
		return TrinaryLogic::createNo();
	}

	public function getDeprecatedDescription(): ?string {
		return null;
	}

	public function isInternal(): TrinaryLogic {
		return TrinaryLogic::createNo();
	}
}

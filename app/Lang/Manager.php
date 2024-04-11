<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Lang;

use Illuminate\Contracts\Filesystem\Filesystem;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\ParserFactory;

class Manager
{
	protected array $originalTranslations = [];

	protected array $translations = [];

	protected array $files = [];

	protected array $stmts = [];

	protected array $tokens = [];

	/**
	 * Create a new class instance.
	 */
	public function __construct(protected Filesystem $fileSystem, protected string $fileName = 'stored') {}

	public function importTranslations(bool $force = false): void
	{
		if (!($force || empty($this->originalTranslations))) {
			if (empty($this->translations))
				$this->translations = $this->originalTranslations;
			return;
		}
		$files = $this->fileSystem->allFiles();

		foreach ($files as $file) {
			if (preg_match('/^([a-z]{2})\\/' . $this->fileName . '\\.php$/', $file, $matches)) {
				$locale = $matches[1];
				$this->files[$locale] = $file;
				$this->originalTranslations[$locale] = $this->loadTranslations($locale, $file);
				$this->translations[$locale] = $this->originalTranslations[$locale];
			}
		}
	}

	public function exportTranslations(bool $force = false): void
	{
		foreach ($this->translations as $locale => $translations) {
			if ($force || $translations !== $this->originalTranslations[$locale])
				$this->fileSystem->put($this->files[$locale], $this->encodeTranslations($locale));
		}
	}

	public function getTranslations(?string $locale = null): array
	{
		if ($locale === null)
			return $this->translations;
		return $this->translations[$locale] ?? [];
	}

	public function getOriginalTranslations(?string $locale = null): array
	{
		if ($locale === null)
			return $this->originalTranslations;
		return $this->originalTranslations[$locale] ?? [];
	}

	public function setTranslations(string $locale, array $translations): void
	{
		array_merge($this->translations[$locale], $translations);
	}

	public function addTranslation(string $locale, string $key, string $value): void
	{
		$this->translations[$locale][$key] = $value;
	}

	public function editTranslation(string $locale, string $key, ?string $value): void
	{
		if ($value === null)
			$this->removeTranslation($locale, $key);
		else $this->addTranslation($locale, $key, $value);
	}

	public function hasTranslation(string $locale, string $key): bool
	{
		return isset($this->translations[$locale][$key]);
	}

	public function hasLocale(string $locale): bool
	{
		return isset($this->translations[$locale]);
	}

	public function setTranslation(string $locale, string $key, ?string $value): void
	{
		$this->editTranslation($locale, $key, $value);
	}

	public function removeTranslation(string $locale, string $key): void
	{
		unset($this->translations[$locale][$key]);
	}

	public function getTranslation(string $locale, string $key): ?string
	{
		return $this->translations[$locale][$key] ?? null;
	}

	public function getTranslationsKeys(string $locale): array
	{
		return array_keys($this->translations[$locale] ?? []);
	}

	public function getTranslationsValues(string $locale): array
	{
		return array_values($this->translations[$locale] ?? []);
	}

	public function getTranslationsCount(string $locale): int
	{
		return \count($this->translations[$locale] ?? []);
	}

	public function getLocales(): array
	{
		return array_keys($this->translations);
	}

	public function getLocalesCount(): int
	{
		return \count($this->translations);
	}

	public function translation(string $key, array|string $locale, ?string $value = null): ?string
	{
		if (\is_array($locale)) {
			if ($value !== null)
				throw new \InvalidArgumentException('Cannot set a value for multiple locales');

			foreach ($locale as $l => $v)
				$translation = $this->translate($key, $l, $v);
			return null;
		}

		if ($value !== null) {
			$this->setTranslation($locale, $key, $value);
			return null;
		}

		return $this->getTranslation($locale, $key) ?? $key;
	}

	public function translate(string $key, array|string $locale, ?string $value = null): void
	{
		if ($value === null && \is_string($locale))
			$this->removeTranslation($key, $locale);
		$this->translation($key, $locale, $value);
	}

	public function getTranslationsForKey(string $key): array
	{
		$translations = [];
		$locales = $this->getLocales();

		foreach ($locales as $locale)
			$translations[$locale] = $this->getTranslation($locale, $key);
		return $translations;
	}

	protected function loadTranslations(string $locale, string $file): array
	{
		$parser = (new ParserFactory())->createForHostVersion();
		$this->stmts[$locale] = $parser->parse($this->fileSystem->get($file));
		$this->tokens[$locale] = $parser->getTokens();
		$retunStmt = null;

		foreach ($this->stmts[$locale] as $stmt) {
			if (!$stmt instanceof Stmt\Return_)
			continue;
			$returnStmt = $stmt->expr;
		}
		if ($returnStmt === null)
			return [];
		$translations = [];

		foreach ($returnStmt->items as $item)
			$translations[$item->key->value] = $item->value->value;
		return $translations;
	}

	protected function encodeTranslations(string $locale): string
	{
		$translations = $this->translations[$locale];
		$traverser = new NodeTraverser(new NodeVisitor\CloningVisitor());
		$stmts = $traverser->traverse($this->stmts[$locale]);
		$items = [];

		foreach ($translations as $key => $value)
			$items[] = new Expr\ArrayItem(new Node\Scalar\String_($value), new Node\Scalar\String_($key));

		foreach ($stmts as $stmt) {
			if (!$stmt instanceof Stmt\Return_)
			continue;
			$stmt->expr = new Expr\Array_($items);
		}
		$prettyPrinter = new \PhpParser\PrettyPrinter\Standard();
		return $prettyPrinter->printFormatPreserving($stmts, $this->stmts[$locale], $this->tokens[$locale]);
	}
}

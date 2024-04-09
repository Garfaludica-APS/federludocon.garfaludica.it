<?php

namespace App\Lang;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;

class Manager
{
	protected array $originalTranslations = [];
	protected array $translations = [];
	protected array $files = [];
	/**
	 * Create a new class instance.
	 */
	public function __construct(protected Application $app, protected Filesystem $fileSystem, protected string $baseFileName = 'messages') { }

	public function importTranslations(bool $force = false): void
	{
		if (!($force || empty($this->originalTranslations))) {
			if (empty($this->translations))
				$this->translations = $this->originalTranslations;
			return;
		}
		$langPath = $this->app['path.lang'];
		$files = $this->fileSystem->allFiles($langPath);
		foreach ($files as $file) {
			$basename = $file->getBasename();
			if (preg_match('/^' . $this->baseFileName . '_([a-z]{2})\.json$/', $basename, $matches)) {
				$locale = $matches[1];
				$this->files[$locale] = $file;
				$this->originalTranslations[$locale] = $this->fileSystem->json($file);
				$this->translations[$locale] = $this->originalTranslations[$locale];
			}
		}
	}

	public function exportTranslations(bool $force = false): void
	{
		$langPath = $this->app['path.lang'];
		foreach ($this->translations as $locale => $translations) {
			if ($force || $translations !== $this->originalTranslations[$locale])
				$this->fileSystem->put($this->files[$locale], json_encode($translations, JSON_PRETTY_PRINT));
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
		else
			$this->addTranslation($locale, $key, $value);
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
		return count($this->translations[$locale] ?? []);
	}

	public function getLocales(): array
	{
		return array_keys($this->translations);
	}

	public function getLocalesCount(): int
	{
		return count($this->translations);
	}

	public function translation(string $key, string|array $locale, ?string $value = null): ?string
	{
		if (is_array($locale)) {
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

	public function translate(string $key, string|array $locale, ?string $value = null): null
	{
		if ($value === null && is_string($locale))
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
}

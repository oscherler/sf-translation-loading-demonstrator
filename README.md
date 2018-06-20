# Symfony Translation Loading Demonstrator

## Introduction

This repository aims at demonstrating that between Symfony 2.6 and Symfony 2.8, changes to the way the translator in the FrameworkBundle loads resources make it seemingly impossible to load translations in an EventSubscriber.

There are 3 quite minimalistic projects to test different versions of Symfony: 2.6.x, 2.8.x , and 4.1.x.

## Concept of the Demonstrator

* Default locale: `fr`;
* Translation in the standard location (`app/Resources/translations/messages.fr.yml` or `translations/messages.fr.yaml`): `foo: toto`;
* Translation in a non standard location (`truc/messages.fr.yml`): `bar: tata`;
* Test command: `app/console test` or `bin/console test`;
* Event subscriber on `console.command` loading the translations from the non standard location: `App\TranslationSubscriber`.

The test command translates two strings, `foo` and `bar`. The translation for `foo` is loaded by the framework from the standard location, and is `toto`. The translation for `bar` is loaded by the event subscriber from the non standard location, and is `tata`.

## Executing the Demonstrator

### Symfony 2.6

	cd sf26
	composer install
	app/console cache:clear
	app/console test
	app/console cache:clear --env=prod
	app/console test --env=prod

Result:

	toto
	tata

	toto
	tata

Expected result:

	toto
	tata

	toto
	tata

Status: success.

### Symfony 2.8

**Note:** the `app/console cache:clear` step is important, as for some reason, running the command directly after `composer install` works the first in `dev`.

	cd sf28
	composer install
	app/console cache:clear
	app/console test
	app/console cache:clear --env=prod
	app/console test --env=prod

Result:

	toto
	bar

	toto
	bar

Expected result:

	toto
	tata

	toto
	tata

Status: failure.

### Symfony 4.1

**Note:** the `app/console cache:clear` step is important, as for some reason, running the command directly after `composer install` works the first in `dev`.

	cd sf41
	composer install
	bin/console cache:clear
	bin/console test
	bin/console cache:clear --env=prod
	bin/console test --env=prod

Result:

	toto
	bar

	toto
	bar

Expected result:

	toto
	tata

	toto
	tata

Status: failure.

## Suspected Cause

After bisecting on tags between v2.6.13 and v2.8.41, it seems that the change occurred between v2.7.4 and v2.7.5. Specifically, this change in `src/Symfony/Bundle/FrameworkBundle/Translation/Translator.php`:

		 /**
		  * {@inheritdoc}
		  */
		 public function warmUp($cacheDir)
		 {
			 // skip warmUp when translator doesn't use cache
			 if (null === $this->options['cache_dir']) {
				 return;
			 }

	-        foreach ($this->resourceLocales as $locale) {
	+        $locales = array_merge($this->getFallbackLocales(), array($this->getLocale()), $this->resourceLocales);
	+        foreach (array_unique($locales) as $locale) {
	+            // reset catalogue in case it's already loaded during the dump of the other locales.
	+            if (isset($this->catalogues[$locale])) {
	+                unset($this->catalogues[$locale]);
	+            }
	+
				 $this->loadCatalogue($locale);
			 }
		 }

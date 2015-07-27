<?php namespace Yaravel\Imagevalidator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;

class ImagevalidatorServiceProvider extends ServiceProvider
{

	/**
	* Indicates if loading of the provider is deferred.
	*
	* @var bool
	*/
	protected $defer = false;

	protected $rules = array(
		'image_size',
		'image_aspect',
	);


	/**
	* Bootstrap the application events.
	*
	* @return void
	*/
	public function boot()
	{
		$this->loadTranslationsFrom(__DIR__.'/../lang', 'imagevalidator');

		$this->app->bind('Yaravel\Imagevalidator\Imagevalidator', function($app)
		{
			$validator = new Imagevalidator($app['translator'], [], [], trans('imagevalidator::validation') );

			if (isset($app['validation.presence']))
			{
				$validator->setPresenceVerifier($app['validation.presence']);
			}

			return $validator;

		});

		$this->addNewRules();
	}


	/**
	* Get the list of new rules being added to the validator.
	* @return array
	*/
	public function getRules()
	{
		return $this->rules;
	}

	/**
	* Returns the translation string depending on laravel version
	* @return string
		*/
	protected function loadTranslator()
	{
		return trans('imagevalidator::validation');
	}

	/**
	* Add new rules to the validator.
	*/
	protected function addNewRules()
	{
		foreach($this->getRules() as $rule)
		{
			$this->extendValidator($rule);
		}
	}


	/**
	* Extend the validator with new rules.
	* @param  string $rule
	* @return void
	*/
	protected function extendValidator($rule)
	{
		$method = studly_case($rule);
		$translation = trans('imagevalidator::validation');
		$this->app['validator']->extend($rule, 'Yaravel\Imagevalidator\Imagevalidator@validate' . $method, $translation[$rule]);
		$this->app['validator']->replacer($rule, 'Yaravel\Imagevalidator\Imagevalidator@replace' . $method );
	}


	/**
	* Register the service provider.
	*
	* @return void
	*/
	public function register()
	{
	}


	/**
	* Get the services provided by the provider.
	*
	* @return array
	*/
	public function provides()
	{
		return [];
	}

}

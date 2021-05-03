# Underpin decision list Loader

Loader That assists with adding decision lists to a WordPress website.

## Installation

### Using Composer

`composer require underpin/decision-list-loader`

### Manually

This plugin uses a built-in autoloader, so as long as it is required _before_
Underpin, it should work as-expected.

`require_once(__DIR__ . '/underpin-decision-lists/decision-lists.php');`

## Setup

1. Install Underpin. See [Underpin Docs](https://www.github.com/underpin-wp/underpin)
1. Register new decision lists as-needed.

## Decision Lists

Typically, WordPress plugins rely solely on WordPress hooks to determine extended logic. This works for simple
solutions, but it becomes cumbersome very fast as soon as several plugins are attempting to override one-another. The
biggest issue is that the actual logic that determines the decision is _decentralized_. Since there isn't a single
source-of-truth to dictate the order of logic, let along what the actual _choices are_, you have no easy way of
understanding _why_ a plugin decided to-do what it did.

Decision lists aim to make this easier to work with by making the extensions all _centralized_ in a single registry.
This registry is exported in the Underpin console when `WP_DEBUG` is enabled, so it is abundantly clear _what_ the
actual hierarchy is for this site.

If you're debugging a live site, you can output the decision list using a PHP console tool, such as debug bar console.

 ```php
 var_dump(plugin_name_replace_me()->decision_lists()->get('email'));
```

### Set Up

Fundamentally a Decision List is nothing more than a loader class, and can be treated in the same way.

Let's say we wanted to create a decision list that allows people to override an email address in a plugin. This would
need to check an options value for an email address, and fallback to a hard-coded address. It also needs to be possible
to override this value with other plugins.

In traditional WordPress, you would probably see this done using `apply_filters` at the end of the function, something
like this:

 ```php
function get_email_address(){
  
  return apply_filters('plugin_name_replace_me_email_address',get_option('email_address', 'admin@webmaster.com'));
}
```

With a decision list, however, this is put inside of a class, and that class can be extended. Like so:

```php

/**
 * Class Email To
 * Class Email to list
 *
 * @since   1.1.0
 * @package DFS_Monitor\Factories
 */
class Email_To extends Decision_List {

	public $dedecision listion = 'Determines which email address this plugin should use.';
	public $name = 'Email Address';

	/**
	 * @inheritDoc
	 */
	protected function set_default_items() {

		$this->add( 'option', new class extends Integration_Frequency_Decision {

			public $id = 'option';
			public $name = 'Option Value';
			public $dedecision listion = 'Uses the value of the db option, if it is set.';
			public $priority = 100;


			public function is_valid( $params = [] ) {
				if(!is_email(get_option('email_address'))){
				  return plugin_name_replace_me()->logger()->log(
				    'notice',
                    'email_address_option_invalid',
                    'A decision tree did not use the option value because it is not set.'
                  );
                } else{
                  return true;  
              }             
			}

			/**
			 * @inheritDoc
			 */
			public function valid_actions( $params = [] ) {
				return get_option('email_address');
			}
		} );


		$this->add( 'hard_coded', new class extends Integration_Frequency_Decision {

			public $id = 'hard_coded';
			public $name = 'Hard coded email';
			public $dedecision listion = 'Uses a hard-coded email address for this site.';
			public $priority = 1000;

			public function is_valid( $params = [] ) {
				return true;
			}

			public function valid_actions( $params = [] ) {
				return 'admin@webmaster.com';
			}
		} );
	}
}
```

Notice that I'm using anonymous classes here, just to keep everything in a single file. You absolutely _do not_ have to
use anonymous classes. In fact, in most cases you shouldn't. If you pass a reference to the class as a string, it will
not instantiate the class unless it's explicitly called. This saves on resources and keeps things fast.

The `$priority` value inside each class tells the decision tree which option to try to use first. If it returns
a `WP_Error`, it moves on to the next one. As soon as it finds an option that returns `true`, it grabs the value from
the `valid_actions` method, and move on.

Like the custom logger class, this needs to be registered inside `Service_Locator`.

```php
	/**
	 * Set up active loader classes.
	 *
	 * This is where you can add anything that needs "registered" to WordPress,
	 * such as shortcodes, rest endpoints, blocks, and cron jobs.
	 *
	 * All supported loaders come pre-packaged with this plugin, they just need un-commented here
	 * to begin using.
	 *
	 * @since 1.0.0
	 */
	protected function _setup() {
      plugin_name_replace_me()->decision_lists()->add('email', '\Plugin_Name_Replace_Me\Decision_Lists\Email_To');
	}
```

Finally, we can use this decision list directly in our `get_email_address` function:

 ```php
function get_email_address(){
  
  // Decide which action we should take.
  $decide = plugin_name_replace_me()->decision_lists()->get('email')->decide();

  // Return the valid decision.
  if(!is_wp_error($decide) && $decide['decision'] instanceof Decision){
    return $decide['decision']->valid_actions();
  }

  // Bubble up the error, otherwise.
  return $decide;
}
```

Now that we have this set up, it can be extended by other plugins using the `add` method. The example below would force
the decision list to run this _before_ any other option.

```php
plugin_name_replace_me()->decision_lists()->get('email')->add('custom_option',new class extends \Underpin\Abstracts\Decision{

  // Force this to run before all other options
  public $priority = 50;
  public $name = 'Custom Option Name';
  public $dedecision listion = 'This custom name is used in an extension, and overrides the default';

  public function is_valid($params = []){
    // TODO: Implement is_valid() method.
  }

  public function valid_actions($params = []){
  // TODO: Implement valid_actions() method.
  }


});
```

## Example

A very basic example could look something like this.

```php
\Underpin\underpin()->decision_lists()->add( 'example_decision_list', [
	[
		// Decision one
		[
			'valid_callback'         => '__return_true',
			'valid_actions_callback' => '__return_empty_string',
			'name'                   => 'Test Decision',
			'description'            => 'A single decision',
			'priority'               => 500,
		],

		// Decision two
		[
			'valid_callback'         => '__return_true',
			'valid_actions_callback' => '__return_empty_array',
			'name'                   => 'Test Decision Two',
			'description'            => 'A single decision',
			'priority'               => 1000,
		],

	],
] );
```

Alternatively, you can extend `decision list` and reference the extended class directly, like so:

```php
underpin()->decision_lists()->add('key','Namespace\To\Class');
```

This is especially useful when using decision lists, since they have a tendency to get quite long, and nest deep.

## Getting Decision Results

When a decision list determines an action, it does three things:

1. Sorts all decisions by priority, smallest numbers first.
1. Loops through each item, and stops on the first decision that passes their respective test.
1. Returns the result of the decision's `valid_actions` callback.

The example above would return `''`, because the first item to pass would be the item with the smallest priority that will pass.

```php
underpin()->decision_lists()->decide('example_decision_list', [] );
```

The second argument, `$params` is an array of arguments that are passed to both the `valid_callback` and `valid_actions_callback`.
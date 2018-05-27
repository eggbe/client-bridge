## Introduction
This is the client part of the lightweight and high-performance library that implements client-to-server and server-to-server communications.       


## Requirements
* PHP >= 7.0.0
* [Eggbe/Helpers](https://github.com/eggbe/helpers)
* [Able/Reglib](https://github.com/phpable/reglib)


## Install
Here's a pretty simple way to start using Eggbe/ClientBridge:


Step 1: Use [Composer](http://getcomposer.org) to add Eggbe/ClientBridge in your project: 

```bash
composer require eggbe/client-bridge
```


Step 2: Create and configure an instance anywhere in your code:

```php
$Bridge = new \Eggbe\ClientBridge\Bridge([
	'url' => 'server-url',
	'method' => \Eggbe\ClientBridge\Bridge::RM_POST,
]);
```

The `method` option be be one of two possible values: Get or Post. By default it set in Get. Other request methods currently are not allowed here. 
   

## Usage
You have to use the following method to create request with custom parameters and send it: 

```php
$Bridge->with('custom-parameter-name', 'custom-parameter-value')->send();
```
 
Also you could use a more smart syntax if you like it. The following code is identical to the previous:  

```php
$Bridge->withCustomParameterName('custom-parameter-value')->send();
```

Unfortunately in now this library is only support the HTTP/HTTPS protocols but we have plans to extend this part of the functionality. We will keep you in touch!


## Authors
Made with love at [Eggbe](http://eggbe.com).


## Feedback 
We always welcome your feedback at [github@eggbe.com](mailto:github@eggbe.com).


## License
This package is released under the [MIT license](https://github.com/eggbe/client-bridge/blob/master/LICENSE).

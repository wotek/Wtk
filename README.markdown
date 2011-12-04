Description
===

In this repository, you're going to find some of my libraries I developed duing my work with Zend Framework. Enjoy!

App_Pdf
---
Wtk_Pdf is an PDF generator library. It uses wkhtmltodf - simple shell utility to convert html to pdf using 
the webkit rendering engine, and qt. Read more at http://code.google.com/p/wkhtmltopdf/

###Installation & configuration
For wkhtmltopdf adapter, you need to download & place script in default path: ```application/scripts/wkhtmltopdf```

Command name and path, are configurable through adapter options array.

Example:
```App_Pdf::factory('wkhtmltopdf', array('command' => 'command_name', 'command_path' => '/path/to/command'));```

###Usage
```php
$pdf = App_Pdf::factory('wkhtmltopdf', array());
$pdf->setDocument('document body'); // It might be HTML rendered by Zend_View & Zend_Layout
$pdf->setDestination('writable_destination');
$pdf->generate();
```


App_Geocoder
---
Geocoder is a wrapper for geocoding services. Currently, only Google geocoding service is implemented.

###Configuration
Geocoder has its own application resource loader. (Storing services API Keys in application config).
Add this to your INI config file:

```
resources.geocoder.options.adapter = 'GoogleMaps';
resources.geocoder.options.apikey = 'your_api_key';
```

###Usage
```
$location = App_Geocoder::factory() // By default it uses adapter configured through application config ini
  				->geocode('address_to_geocode')
					->getLocations() // Returns matched locations container
					->current() // Returns first (probably most accurate) location
					;
```
For more information look in class definitions.

App_Mail
---
Simple wrapper for Zend_Mail class. 



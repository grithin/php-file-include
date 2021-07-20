# FileInclude
For including files in an isolated context, but with injected variables, and potentially extracted variables.


```sh
composer require grithin/file-include
```


## Use
```php
use \Grithin\FileInclude;
# inject `bob` into the file context, so file code can access $bob
FileInclude::include('file.php', ['bob'=>$bob]);

# inject the global $bob into the file
global $bob;
FileInclude::require_once('file2.php', null, ['globals'=>['bob']]);
```

### Extraction
Extraction will adjust the return value.  Whereas, normally, the return value is the return of the file, when variables are being extracted, the return changes into an array, where the normal return can be found in `['_return'=>$x]`

file.php
```php
$bob = 'bob';
return 123;
```
```php
$result = FileInclude::include_once('file.php', null, ['extract'=>['bob']]);
/*>
[
	'_return'=>123,
	'bob'=>'bob'
]
*/
```



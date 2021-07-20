<?php
namespace Grithin;
use Grithin\Debug;

///Used to keep track of inclusions and to get better errors on failed requires
/**
	@note	For this case, since phpe doesn't like it when you use "include" or "require" for method names, I have abbreviated the names.
*/
class FileInclude{
	/** include a file */
	/**
	@param	_file	file path
	@param	_vars	[ < name > : < value >, ...]  < array of keyed variables to extract into the file >
	@param	_options	[
			extract: < variables to extract from the file context >
			globals: < string-names of variables to introduce to the file as global variables >
		]

	@return
		if successful
			if _options['extract']
				keyed extracted variable array
			else
				value from the inclusion function (generally `1` if the file does not explicitly use a return statement)
		if not successful
			false


	@Example
		File `bob.php`:
			<?php
			$bill = [$bob]
			$bill[] = 'monkey'
			return 'blue'
		Use
			Files::inc('bob.php')
			#< 'blue'

			Files::inc('bob.php',['bob'=>'sue'], ['extract'=>['bill']])
			#< ['sue', 'monkey']



	*/
	/** note.md
	Reference is not maintained during extraction:
	```php
	$x = 'bill';
	$a = ['y'=>&$x];
	extract($a);
	$y = 'bob';
	# $x is still 'bill'
	$a['y'] = 'bob';
	# $x is now 'bob'
	```

	However, what can be done is re-assigning values after inclusion:
	```php
	$_var = ['bob'=>$x];
	$_options = ['extract'=>array_keys($_vars)];

	This will cause the return value to be the extracted vars into a returned array.  The actual return value from the file will be put into '_return'
	```

	*/
	protected static function get($_file, $_vars=null, $_options=[]){
		if(!empty($_options['globals'])){
			foreach($_options['globals'] as $_global){
				global $$_global;
			}
		}
		if($_vars){
			extract($_vars,EXTR_SKIP);#don't overwrite existing
		}

		$_return = include($_file);

		if(!empty($_options['extract'])){
			$_return = ['_return'=>$_return];
			foreach($_options['extract'] as $_var){
				$_return[$_var] = $$_var;
			}
			return $_return;
		}
		return $_return;
	}
	protected static function once($_file, $_vars=null, $_options=[]){
		if(!empty($_options['globals'])){
			foreach($_options['globals'] as $_global){
				global $$_global;
			}
		}
		if($_vars){
			extract($_vars,EXTR_SKIP);#don't overwrite existing
		}

		$_return = include_once($_file);

		if(!empty($_options['extract'])){
			$_return = ['_return'=>$_return];
			foreach($_options['extract'] as $_var){
				$_return[$_var] = $$_var;
			}
			return $_return;
		}
		return $_return;
	}


	public static function include($file, $vars=null, $options=[]){
		if(is_file($file)){
			return self::get($file, $vars, $options);
		}
		return false;
	}
	/** include a file once */
	public static function include_once($file, $vars=null, $options=[]){
		if(is_file($file)){
			return self::once($file, $vars, $options);
		}
		return false;
	}
	/** require a file */
	/**
	see self::include
	@return	on failure, runs throw new \Exception
	*/
	public static function require($file, $vars=null, $options=[]){
		if(is_file($file)){
			return self::get($file, $vars, $options);
		}
		throw new \Exception('Could not include file "'.$file.'"');
	}
	/** require a file once */
	/**
	see self::include
	@return	on failure, runs throw new \Exception
	*/
	public static function require_once($file, $vars=null, $options=[]){
		if(is_file($file)){
			return self::once($file, $vars, $options);
		}
		throw new \Exception('Could not include file "'.$file.'"');
	}
}



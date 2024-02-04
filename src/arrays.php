<?php declare(strict_types=1);

/* ==== LICENCE AGREEMENT =====================================================
 *
 * © Cédric Ducarre (20/05/2010)
 * 
 * wlib is a set of tools aiming to help in PHP web developpement.
 * 
 * This software is governed by the CeCILL license under French law and
 * abiding by the rules of distribution of free software. You can use, 
 * modify and/or redistribute the software under the terms of the CeCILL
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 * 
 * As a counterpart to the access to the source code and rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty and the software's author, the holder of the
 * economic rights, and the successive licensors have only limited
 * liability.
 * 
 * In this respect, the user's attention is drawn to the risks associated
 * with loading, using, modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean that it is complicated to manipulate, and that also
 * therefore means that it is reserved for developers and experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or 
 * data to be ensured and, more generally, to use and operate it in the 
 * same conditions as regards security.
 * 
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
 * 
 * ========================================================================== */

/**
 * Helpers for arrays.
 *
 * @author Cédric Ducarre
 * @since 28/01/2013
 */

/**
 * Check if a key exists in the given array.
 *
 * Return `false` without raising an error if `$aArray` is not an array.
 *
 * @param array|ArrayAccess $aArray Concerned array.
 * @param int|string $mKey Key to check.
 * @return bool
 */
function isArrayKey(array|ArrayAccess $aArray, int|string $mKey): bool
{
	if (!is_array($aArray))
	{
		if (is_a($aArray, 'ArrayAccess'))
			return isset($aArray[$mKey]);

		return false;
	}

	if (is_int($mKey))
		return array_key_exists($mKey, $aArray);

	$aNames = explode('.', $mKey);
	$aCursor = &$aArray;

	foreach ($aNames as $sKeyName)
	{
		if (!array_key_exists($sKeyName, $aCursor))
			return false;

		$aCursor = &$aCursor[$sKeyName];
	}

	return true;
}

/**
 * Access safely to an array value from its key or return default value.
 *
 * @param array|ArrayAccess $aArray Source array.
 * @param int|string $mKey Key to access.
 * @param mixed|null $mDefault Default value if key is not found.
 * @return mixed|null
 */
function arrayValue(array|ArrayAccess &$aArray, int|string $mKey, mixed $mDefault = null): mixed
{
	return (isArrayKey($aArray, $mKey) ? $aArray[$mKey] : $mDefault);
}

/**
 * Extends the first array with the others given arrays.
 *
 * For a common key, the value kept is the one in the last array.
 *
 * This method is an equivalent to the jQuery.extend() method.
 *
 * @param array $aTarget Target array.
 * @param array ...$aArray Arrays to merge.
 * @return void
 */
function arrayExtend(array &$aTarget, array ...$aArrays): void
{
	foreach ($aArrays as $aArray)
	{
		reset($aTarget);

		foreach ($aArray as $mKey => $mValue)
		{
			$targetval = arrayValue($aTarget, $mKey);

			if (is_array($mValue) && is_array($targetval))
				arrayExtend($aTarget[$mKey], $mValue);

			else
				$aTarget[$mKey] = $mValue;
		}
	}
}

/**
 * Access to items of an array, even hierarchical.
 *
 * `access()` is a generic accessor to safely manipulate items in an array.
 * 
 * It verifies array keys existences for you.
 *
 * ## Get an item
 *
 * ```php
 * access($array, 'keyname');
 * access($array, 'keyname', 'default value if keyname is not set');
 * ```
 *
 * ### Get an subitem
 *
 * ```php
 * access($array, 'level1.level2.level3');
 * // Equivalent to $array['level1']['level2']['level3'];
 * ```
 *
 * ## Set an item
 *
 * Writing in an array can be achieved by passing an array to `$mName` parameter.
 * `$mDefault` parameter is then ignored.
 * 
 * ```php
 * // One value
 * access($array, ['keyname' => 'keyvalue']);
 *
 * // Several values
 * access($array, [
 * 	'key1' => 'value1',	// Equivalent to $array['key1'] = 'value1';
 * 	'key2' => 'value2',
 * 	'a.b.c' => 'value3'	// Equivalent to $array['a']['b']['c'] = 'value3';
 * ]);
 * ```
 *
 * ## Unset an item
 *
 * Use `unaccess($array, 'key1')` to unset an item from the array.
 *
 * @see `unaccess()` for unsetting.
 * @param array $aCursor Array from which the accessor must work.
 * @param string|array $mName Name of item to get or array of items to add. If `$mName` isn't used, the full array is returned.
 * @param mixed $mDefault Default value if `$mName` is a string which doesn't exist in the array.
 * @return mixed|boolean mixed if `$mName` is a `string`, boolean in writing mode.
 */
function access(&$aCursor, string|array $mName = '', mixed $mDefault = null): mixed
{
	if (!is_array($aCursor))
		return $mDefault;

	if (is_array($mName))
	{
		foreach ($mName as $mKey => $mValue)
		{
			$aNames = explode('.', $mKey);
			$aTmpCursor = &$aCursor;

			foreach ($aNames as $sKeyName)
			{
				if (!is_array($aTmpCursor))
					$aTmpCursor = array();

				if (!array_key_exists($sKeyName, $aTmpCursor))
					$aTmpCursor[$sKeyName] = null;

				$aTmpCursor = &$aTmpCursor[$sKeyName];
			}

			$aTmpCursor = $mValue;
		}

		return true;
	}

	if (empty($mName))
		return $aCursor;

	$aNames = explode('.', $mName);

	foreach ($aNames as $sKeyName)
	{
		if (!is_array($aCursor))
			return $mDefault;

		if (!array_key_exists($sKeyName, $aCursor))
			return $mDefault;

		$aCursor = &$aCursor[$sKeyName];
	}

	return $aCursor;
}

/**
 * Access to superglobal $GLOBALS.
 *
 * @see `access()` to learn how to use.
 * @see `unglobals()` for unsetting.
 * @param string|array $mName Name of item to get or array of items to add. If `$mName` isn't used, the full array is returned.
 * @param mixed $mDefault Default value if `$mName` is a string which doesn't exist in `$GLOBALS`.
 * @return mixed|boolean mixed if `$mName` is a `string`, boolean in writing mode.
 */
function globals(string|array $mName = '', mixed $mDefault = null): mixed
{
	if (empty($mName))
		return $GLOBALS;

	if (is_array($mName))
	{
		foreach ($mName as $mKey => $mValue)
		{
			$aNames = explode('.', $mKey);
			$sCursor = array_shift($aNames);

			global $$sCursor;
			$aTmpCursor = &$$sCursor;

			foreach ($aNames as $sKeyName)
			{
				if (!is_array($aTmpCursor))
					$aTmpCursor = array();

				if (!array_key_exists($sKeyName, $aTmpCursor))
					$aTmpCursor[$sKeyName] = null;

				$aTmpCursor = &$aTmpCursor[$sKeyName];
			}

			$aTmpCursor = $mValue;
		}

		return true;
	}

	$aNames = explode('.', $mName);
	$sVarName = array_shift($aNames);

	if (!isset($GLOBALS[$sVarName]))
		return $mDefault;

	global $$sVarName;
	
	return (count($aNames)
		? access($$sVarName, implode('.', $aNames), $mDefault)
		: $$sVarName
	);
}

/**
 * Access to superglobal $_SERVER.
 *
 * @see `access()` to learn how to use.
 * @see `unserver()` for unsetting.
 * @param string|array $mName Name of item to get or array of items to add. If `$mName` isn't used, the full array is returned.
 * @param mixed $mDefault Default value if `$mName` is a string which doesn't exist in `$_SERVER`.
 * @return mixed|boolean mixed if `$mName` is a `string`, boolean in writing mode.
 */
function server(string|array $mName = '', mixed $mDefault = null): mixed
{
	return access($_SERVER, $mName, $mDefault);
}

/**
 * Access to superglobal $_GET.
 *
 * @see `access()` to learn how to use.
 * @see `unget()` for unsetting.
 * @param string|array $mName Name of item to get or array of items to add. If `$mName` isn't used, the full array is returned.
 * @param mixed $mDefault Default value if `$mName` is a string which doesn't exist in `$_GET`.
 * @return mixed|boolean mixed if `$mName` is a `string`, boolean in writing mode.
 */
function get(string|array $mName = '', mixed $mDefault = null): mixed
{
	return access($_GET, $mName, $mDefault);
}

/**
 * Access to superglobal $_POST.
 *
 * @see `access()` to learn how to use.
 * @see `unpost()` for unsetting.
 * @param string|array $mName Name of item to get or array of items to add. If `$mName` isn't used, the full array is returned.
 * @param mixed $mDefault Default value if `$mName` is a string which doesn't exist in `$_POST`.
 * @return mixed|boolean mixed if `$mName` is a `string`, boolean in writing mode.
 */
function post(string|array $mName = '', mixed $mDefault = null): mixed
{
	return access($_POST, $mName, $mDefault);
}

/**
 * Access to superglobal $_FILES.
 *
 * @see `access()` to learn how to use.
 * @see `unfiles()` for unsetting.
 * @param string|array $mName Name of item to get or array of items to add. If `$mName` isn't used, the full array is returned.
 * @param mixed $mDefault Default value if `$mName` is a string which doesn't exist in `$_FILES`.
 * @return mixed|boolean mixed if `$mName` is a `string`, boolean in writing mode.
 */
function files(string|array $mName = '', mixed $mDefault = null): mixed
{
	return access($_FILES, $mName, $mDefault);
}

/**
 * Access to superglobal $_COOKIE.
 *
 * Write access is not enable for cookies. It needs to be done by using the PHP
 * `setcookie()` function.
 *
 * @see `access()` to learn how to use.
 * @see `uncookie()` for unsetting.
 * @param string $sName Name of item to get. If `$mName` isn't used, the full array is returned.
 * @param mixed $mDefault Default value if `$mName` is a string which doesn't exist in `$_COOKIE`.
 * @return mixed
 */
function cookie(string $sName = '', mixed $mDefault = null): mixed
{
	return access($_COOKIE, $sName, $mDefault);
}

/**
 * Access to superglobal $_SESSION.
 *
 * @param string|array $mName Name of item to get or array of items to add. If `$mName` isn't used, the full array is returned.
 * @param mixed $mDefault Default value if `$mName` is a string which doesn't exist in `$_FILES`.
 * @return mixed|boolean mixed if `$mName` is a `string`, boolean in writing mode.
 **/
function session(string|array $mName = '', mixed $mDefault = null): mixed
{
	return access($_SESSION, $mName, $mDefault);
}

/**
 * Access to superglobal $_REQUEST.
 *
 * @see `access()` to learn how to use.
 * @see `unrequest()` for unsetting.
 * @param string|array $mName Name of item to get or array of items to add. If `$mName` isn't used, the full array is returned.
 * @param mixed $mDefault Default value if `$mName` is a string which doesn't exist in `$_REQUEST`.
 * @return mixed|boolean mixed if `$mName` is a `string`, boolean in writing mode.
 */
function request(string|array $mName = '', mixed $mDefault = null): mixed
{
	return access($_REQUEST, $mName, $mDefault);
}

/**
 * Access to superglobal $_ENV.
 *
 * @see `access()` to learn how to use.
 * @see `unenv()` for unsetting.
 * @param string|array $mName Name of item to get or array of items to add. If `$mName` isn't used, the full array is returned.
 * @param mixed $mDefault Default value if `$mName` is a string which doesn't exist in `$_ENV`.
 * @return mixed|boolean mixed if `$mName` is a `string`, boolean in writing mode.
 */
function env(string|array $mName = '', mixed $mDefault = null): mixed
{
	return access($_ENV, $mName, $mDefault);
}

/**
 * Access to superglobal $_CONFIG.
 * 
 * ## Prerequisites
 * 
 * This function needs the definition of the item `$_CONFIG['__include_paths']`.
 * It can be one path or an array of paths.
 *
 * @param string $sName Name of item to get. If `$mName` isn't used, the full array is returned.
 * @param mixed $mDefault Default value if `$mName` is a string which doesn't exist in `$_COOKIE`.
 * @return mixed
 * @throws UnexpectedValueException in case of errors whith include(s) path(s).
 * @throws LogicException if a config file doesn't return an array.
 */
function config(string $sName = '', mixed $mDefault = null): mixed
{
	$mIncludePaths = globals('_CONFIG.__include_paths');

	if (!$mIncludePaths)
	{
		return $mDefault;
	}
	
	if (is_string($mIncludePaths))
		$aPaths = [$mIncludePaths];
	elseif (is_array($mIncludePaths))
		$aPaths = $mIncludePaths;
	else
	{
		throw new \UnexpectedValueException(
			'Global `$_CONFIG[\'__include_paths\']` should be a string'
			.' or an array or a string.'
		);
	}

	$aNames = explode('.', $sName);
	$sFileName = array_shift($aNames);

	if (!isset($GLOBALS['_CONFIG'][$sFileName]))
	{
		$aMergedConfig = [];
		foreach ($aPaths as $sDir)
		{
			$sFilePath = $sDir.'/'.$sFileName.'.php';
			if (file_exists($sFilePath))
			{
				$aConfig = include_once $sFilePath;

				if (!is_array($aConfig))
				{
					throw new \LogicException(
						'Config file "'.$sFilePath.'" should return an array.'
					);
				}

				$aMergedConfig = array_merge($aMergedConfig, $aConfig);
			}
			else throw new \UnexpectedValueException(
				'Config path "'.$sFilePath.'" not found.'
			);
		}

		$GLOBALS['_CONFIG'][$sFileName] = $aMergedConfig;
	}

	return access($GLOBALS['_CONFIG'][$sFileName], implode('.', $aNames), $mDefault);
}

/**
 * Add include paths for `config()` function.
 * 
 * @param string|array $mPath Include path or array of include paths to add.
 */
function addConfigIncludePath(string|array $mPath)
{
	if (!isset($GLOBALS['_CONFIG']))
		$GLOBALS['_CONFIG'] = [];

	if (!isset($GLOBALS['_CONFIG']['__include_paths']))
		$GLOBALS['_CONFIG']['__include_paths'] = [];

	if (is_string($mPath))
		$mPath = [$mPath];

	foreach ($mPath as $sPath)
	{
		if (array_search($sPath, $GLOBALS['_CONFIG']['__include_paths']) === false)
			$GLOBALS['_CONFIG']['__include_paths'][] = $sPath;
	}
}

/**
 * Delete values from an array.
 *
 * It's like `access()` but for deleting.
 * 
 * # Usage
 *
 * ```php
 * unaccess($array, 'key1');					// Equivalent to unset($array['key1'])
 * unaccess($array, 'a.b.c');					// Equivalent to unset($array['a']['b']['c']);
 * unaccess($array, 'key2', 'key3', 'a.b.c');	// Equivalent to unset($array['key2'], $array['key3'], $array['a']['b']['c']);
 * ```
 *
 * @param array $aCursor Array from which the accessor must work.
 * @param string|int ...$mKey Keys of values to delete. Use strings with dots to access to hierachical values.
 */
function unaccess(array &$aCursor, string|int ...$mKey): void
{
	$aArgs = func_get_args();
	array_shift($aArgs);

	foreach ($aArgs as $mKey)
	{
		if (is_string($mKey))
		{
			$aKeys = explode('.', $mKey);
			$iLevels = sizeof($aKeys) - 1;
	
			for ($i = 0; $i < $iLevels; $i++)
			{
				if (!is_array($aCursor))
					continue 2;
	
				if (!array_key_exists($aKeys[$i], $aCursor))
					continue 2;
	
				$aCursor = &$aCursor[$aKeys[$i]];
			}
	
			if (!is_array($aCursor))
				continue;
	
			if (!array_key_exists($aKeys[$i], $aCursor))
				continue;
	
			unset($aCursor[$aKeys[$i]]);
		}
		elseif (is_int($mKey) && array_key_exists($mKey, $aCursor))
			unset($aCursor[$mKey]);
	}
}

/**
 * Delete values from $GLOBALS.
 *
 * @see unaccess() to learn how to use.
 * @param string|int ...$mKey Keys of values to delete. Use strings with dots to access to hierachical values.
 */
function unglobals(string|int $mKey): void
{
	$aArgs = func_get_args();

	foreach ($aArgs as $mKey)
	{
		$aNames = explode('.', $mKey);
		$iCount = count($aNames);
		$sVarName = array_shift($aNames);
	
		if (!isset($GLOBALS[$sVarName]))
			return;

		if ($iCount<= 1)
			unset($GLOBALS[$sVarName]);
		else
		{
			global $$sVarName;
			call_user_func_array('unaccess', array_merge(array(&$$sVarName), $aNames));
		}
	}
}

/**
 * Delete values from $_SERVER.
 *
 * @see unaccess() to learn how to use.
 * @param string|int ...$mKey Keys of values to delete. Use strings with dots to access to hierachical values.
 */
function unserver(string|int $mKey): void
{
	call_user_func_array('unaccess', array_merge(array(&$_SERVER), func_get_args()));
}

/**
 * Delete values from $_GET.
 *
 * @see unaccess() to learn how to use.
 * @param string|int ...$mKey Keys of values to delete. Use strings with dots to access to hierachical values.
 */
function unget(string|int $mKey): void
{
	call_user_func_array('unaccess', array_merge(array(&$_GET), func_get_args()));
}

/**
 * Delete values from $_POST.
 *
 * @see unaccess() to learn how to use.
 * @param string|int ...$mKey Keys of values to delete. Use strings with dots to access to hierachical values.
 */
function unpost(string|int $mKey): void
{
	call_user_func_array('unaccess', array_merge(array(&$_POST), func_get_args()));
}

/**
 * Delete values from $_FILES.
 *
 * @see unaccess() to learn how to use.
 * @param string|int ...$mKey Keys of values to delete. Use strings with dots to access to hierachical values.
 */
function unfiles(string|int $mKey): void
{
	call_user_func_array('unaccess', array_merge(array(&$_FILES), func_get_args()));
}

/**
 * Delete values from $_COOKIE.
 *
 * @see unaccess() to learn how to use.
 * @param string|int ...$mKey Keys of values to delete. Use strings with dots to access to hierachical values.
 */
function uncookie(string|int $mKey): void
{
	call_user_func_array('unaccess', array_merge(array(&$_COOKIE), func_get_args()));
}

/**
 * Delete values from $_SESSION.
 *
 * @see unaccess() to learn how to use.
 * @param string|int ...$mKey Keys of values to delete. Use strings with dots to access to hierachical values.
 */
function unsession(string|int $mKey): void
{
	call_user_func_array('unaccess', array_merge(array(&$_SESSION), func_get_args()));
}

/**
 * Delete values from $_REQUEST.
 *
 * @see unaccess() to learn how to use.
 * @param string|int ...$mKey Keys of values to delete. Use strings with dots to access to hierachical values.
 */
function unrequest(string|int $mKey): void
{
	call_user_func_array('unaccess', array_merge(array(&$_REQUEST), func_get_args()));
}

/**
 * Delete values from $_ENV.
 *
 * @see unaccess() to learn how to use.
 * @param string|int ...$mKey Keys of values to delete. Use strings with dots to access to hierachical values.
 */
function unenv(string|int $mKey): void
{
	call_user_func_array('unaccess', array_merge(array(&$_ENV), func_get_args()));
}

/**
 * Get normalized array of uploaded file(s) instead of $_FILES crappy default structure.
 * 
 * Thanks to Mrten for this usefull function !
 * 
 * @see https://gist.github.com/Mrten
 * @see https://gist.github.com/umidjons/9893735?permalink_comment_id=3495051#gistcomment-3495051
 * @return array
 */
function getUploadedFiles(): array
{
	$return = [];

	foreach ($_FILES as $key => $aFile)
	{
		if (isset($aFile['name']) && is_array($aFile['name']))
		{
			$aNormalized = [];

			foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $k)
			{
				array_walk_recursive(
					$aFile[$k],
					function (&$data, $key, $k)
					{
						$data = [$k => $data];
					},
					$k
				);

				$aNormalized = array_replace_recursive($aNormalized, $aFile[$k]);
			}

			$return[$key] = $aNormalized;
		}
		else $return[$key] = $aFile;
	}

	return $return;
}
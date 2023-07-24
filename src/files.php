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
 * Helpers for files and folders.
 *
 * @author Cédric Ducarre
 * @since 07/11/2013
 */

/**
 * Create a folder if it doesn't already exists.
 *
 * This function uses the recursive mode of `mkdir()`.
 *
 * @param string $sDirName Full directory path.
 * @param integer $iMode Right access value.
 * @return boolean
 */
function createDir(string $sDirName, int $iMode = 0644): bool
{
	if (!is_dir($sDirName))
	{
		mkdir($sDirName, $iMode, true);

		if (!is_dir($sDirName))
			return false;;
	}

	return true;
}

/**
 * Remove a directory recursively.
 *
 * @param string $sDirPath Full path of the directory.
 * @param boolean $bOnlyContent True to remove only content and keep the directory.
 * @return boolean
 */
function removeDir(string $sDirPath, bool $bOnlyContent = false): bool
{
	$oDir = new DirectoryIterator($sDirPath);

	foreach ($oDir as $oItem)
	{
		if ($oItem->isFile() || $oItem->isLink())
			if (!unlink($oItem->getPathName()))
				return false;

			elseif ($oItem->isDot() && $oItem->isDir())
				if (!removeDir($oItem->getPathName()))
					return false;
	}

	unset($oDir, $oItem);

	if (!$bOnlyContent)
		return rmdir($sDirPath);

	return true;
}

/**
 * Empty a directory (don't remove it).
 * 
 * `removeDir()` alias with `$bOnlyContent` forced to `true`.
 * 
 * @param string $sDirPath Full path of the directory.
 * @return boolean
 */
function emptyDir(string $sDirPath): bool
{
	return removeDir($sDirPath, true);
}

/**
 * Add '/' or '\' to the end of the given path if needed.
 *
 * @param string $sDirPath Directory path.
 * @return string
 */
function makeCanonical(string $sPath): string
{
	$cLast = substr($sPath, -1);

	return ($cLast != '/' && $cLast != '\\'
		? $sPath . DIRECTORY_SEPARATOR
		: $sPath
	);
}

/**
 * Walk a directory and run a closure on each file found.
 * 
 * @param string $sFromPath Directory to walk.
 * @param \closure $mCallback Callback to run.
 * @param array $aParams Parameters to pass to the closure.
 * @param boolean $bRecursive `true` to walk recursively.
 * @return boolean
 */
function walkDir(
	string $sFromPath, Closure $mCallback,
	array $aParams = array(), bool $bRecursive = false): bool
{
	if ($bRecursive)
	{
		$oIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
			$sFromPath,
			FilesystemIterator::KEY_AS_PATHNAME |
				FilesystemIterator::CURRENT_AS_FILEINFO
		));
	}
	else
	{
		$oIterator = new FilesystemIterator(
			$sFromPath,
			FilesystemIterator::KEY_AS_PATHNAME |
				FilesystemIterator::CURRENT_AS_FILEINFO
		);
	}

	foreach ($oIterator as $key => $oItem)
	{
		if ($oItem->isFile())
			call_user_func_array($mCallback, array_merge([&$oItem], $aParams));

		$oItem = null;
	}

	return true;
}

/**
 * Get file content if it exists.
 *
 * @param string $sFilePath File full path.
 * @return string|false File content of `false`.
 */
function fileGetContent(string $sFilePath): string|false
{
	if (!is_file($sFilePath))
		return false;

	return file_get_contents($sFilePath, false);
}

/**
 * Get file creation date.
 * 
 * @param string $sFilePath File full path.
 * @return integer|false UNIX timestamp or `false`.
 */
function fileGetBirthTime(string $sFilePath): string|false
{
	if (strtolower(substr(PHP_OS, 0, 3)) === 'win')
		return filectime($sFilePath);

	else
	{
		if ($handle = popen('stat -f %B '. escapeshellarg($sFilePath), 'r'))
		{
			$iBTime = trim(fread($handle, 100));
			pclose($handle);

			return (int) $iBTime;
		}
	}

	return false;
}

/**
 * Put file content if it is writeable.
 * 
 * Create the parent directories if needed.
 *
 * @param string $sFilePath File full path.
 * @param string $sContent Content to write.
 * @param integer $iCreateDirMode Right access for the parent directories creation.
 * @return boolean
 */
function filePutContent(string $sFilePath, string $sContent, $iCreateDirMode = 0644): bool
{
	$sDirPath = dirname($sFilePath);

	createDir($sDirPath, $iCreateDirMode);

	if (!is_writeable($sDirPath) || !is_dir($sDirPath))
		return false;

	return file_put_contents($sFilePath, $sContent);
}
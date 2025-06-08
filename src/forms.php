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
 * Helpers for managing forms.
 *
 * @author Cédric Ducarre
 * @since 09/06/2025
 */

/**
 * Get the HTML checked attribute if values are identicals.
 *
 * @param mixed $mValue Field value.
 * @param mixed $mCompare Value to compare.
 * @return string HTML attribute or empty string.
 */
function checked(mixed $mValue, mixed $mCompare = true)
{
	return __csdred($mValue, $mCompare, 'checked');
}

/**
 * Get the HTML selected attribute if values are identicals.
 *
 * @param mixed $mValue Field value.
 * @param mixed $mCompare Value to compare.
 * @return string HTML attribute or empty string.
 */
function selected(mixed $mValue, mixed $mCompare = true)
{
	return __csdred($mValue, $mCompare, 'selected');
}

/**
 * Get the HTML disabled attribute if values are identicals.
 *
 * @param mixed $mValue Field value.
 * @param mixed $mCompare Value to compare.
 * @return string HTML attribute or empty string.
 */
function disabled(mixed $mValue, mixed $mCompare = true)
{
	return __csdred($mValue, $mCompare, 'disabled');
}

/**
 * Get the HTML readonly attribute if values are identicals.
 *
 * @param mixed $mValue Field value.
 * @param mixed $mCompare Value to compare.
 * @return string HTML attribute or empty string.
 */
function readonly(mixed $mValue, mixed $mCompare = true)
{
	return __csdred($mValue, $mCompare, 'readonly');
}

/**
 * Private helper for checked(), selected(), disabled() and readonly() helpers.
 *
 * @param mixed $mValue Field value.
 * @param mixed $mCompare Value to compare.
 * @param string $sAttr Attribute to generate.
 * @return string HTML attribute or empty string.
 */
function __csdred(mixed $mValue, mixed $mCompare, string $sAttr)
{
	return ((string) $mValue === (string) $mCompare ? "$sAttr=\"$sAttr\"" : '');
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
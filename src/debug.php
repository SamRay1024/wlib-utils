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
 * Dump and continue.
 * 
 * @param mixed ...$var
 * @return void
 */
function vd(...$var)
{
	$vars = func_get_args();

	if (sizeof($vars) <= 0)
		return;

	$bRenderHtml = PHP_SAPI != 'cli';

	if ($bRenderHtml)
		echo '<div class="dump" style="margin:6px;font-family:monospace;font-size:13px">'
			.'<pre style="margin:0;padding:6px;background:#efefef;border:1px solid #e0e0e0;color:#333">';

	foreach ($vars as $idx => $var)
	{
		if ($bRenderHtml && $idx > 0)
			echo '<hr style="height:1px;border:none;color:#efefef;background-color:#e0e0e0;">';

		if (count($vars) > 1)
			echo '<small style="color:#bbb">arg#'.$idx.' »</small> ';

		var_dump($var);
	}

	if ($bRenderHtml)
	{
		echo '</pre>';

		$call = debug_backtrace()[0];

		if ($call['file'] == __FILE__)
			$call = debug_backtrace()[1];

		echo '<small style="color:#069">';
		echo "↳ <strong>File:</strong> {$call['file']} - <strong>Line:</strong> {$call['line']}";
		echo ' - <strong>Time:</strong> '. date('r');
		echo '</small></div>';
	}
}

/**
 * Dump and die.
 * 
 * @param mixed ...$var
 * @return void
 */
function vdd(...$var)
{
	vd(...$var);
	exit();
}
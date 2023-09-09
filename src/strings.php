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
 * Helpers for strings.
 *
 * @author Cédric Ducarre
 * @since 28/01/2013
 */

/**
 * Check if a string is in UTF8 encoding.
 *
 * NOTE : this function search 5 bytes sequences, UTF-8 has 4 bytes sequences
 * of maximum length.
 *
 * @author WordPress
 *
 * @param string $sString The string to be checked.
 * @return bool
 */
function isUTF8($sString)
{
	$iLength = strlen($sString);

	for ($i = 0; $i < $iLength; $i++)
	{
		$c = ord($sString[$i]);

		if ($c < 0x80) $n = 0; # 0bbbbbbb
		elseif (($c & 0xE0) == 0xC0) $n = 1; # 110bbbbb
		elseif (($c & 0xF0) == 0xE0) $n = 2; # 1110bbbb
		elseif (($c & 0xF8) == 0xF0) $n = 3; # 11110bbb
		elseif (($c & 0xFC) == 0xF8) $n = 4; # 111110bb
		elseif (($c & 0xFE) == 0xFC) $n = 5; # 1111110b
		else return false; # Does not match any model

		// n bytes matching 10bbbbbb follow ?
		for ($j = 0; $j < $n; $j++)
		{
			if ((++$i == $iLength) || ((ord($sString[$i]) & 0xC0) != 0x80))
				return false;
		}
	}

	return true;
}

/**
 * Remove accents from the given string.
 *
 * @author WordPress
 * 
 * @param string $sString String to clean.
 * @return string
 */
function removeAccents($sString)
{
	if (!preg_match('/[\x80-\xff]/', $sString))
		return $sString;

	if (isUTF8($sString))
	{
		$chars = array(

			// Decompositions for Latin-1 Supplement
			chr(194) . chr(170) => 'a', chr(194) . chr(186) => 'o',
			chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
			chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
			chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
			chr(195) . chr(134) => 'AE', chr(195) . chr(135) => 'C',
			chr(195) . chr(136) => 'E', chr(195) . chr(137) => 'E',
			chr(195) . chr(138) => 'E', chr(195) . chr(139) => 'E',
			chr(195) . chr(140) => 'I', chr(195) . chr(141) => 'I',
			chr(195) . chr(142) => 'I', chr(195) . chr(143) => 'I',
			chr(195) . chr(144) => 'D', chr(195) . chr(145) => 'N',
			chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
			chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
			chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
			chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
			chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
			chr(195) . chr(158) => 'TH', chr(195) . chr(159) => 's',
			chr(195) . chr(160) => 'a', chr(195) . chr(161) => 'a',
			chr(195) . chr(162) => 'a', chr(195) . chr(163) => 'a',
			chr(195) . chr(164) => 'a', chr(195) . chr(165) => 'a',
			chr(195) . chr(166) => 'ae', chr(195) . chr(167) => 'c',
			chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
			chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
			chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
			chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
			chr(195) . chr(176) => 'd', chr(195) . chr(177) => 'n',
			chr(195) . chr(178) => 'o', chr(195) . chr(179) => 'o',
			chr(195) . chr(180) => 'o', chr(195) . chr(181) => 'o',
			chr(195) . chr(182) => 'o', chr(195) . chr(184) => 'o',
			chr(195) . chr(185) => 'u', chr(195) . chr(186) => 'u',
			chr(195) . chr(187) => 'u', chr(195) . chr(188) => 'u',
			chr(195) . chr(189) => 'y', chr(195) . chr(190) => 'th',
			chr(195) . chr(191) => 'y', chr(195) . chr(152) => 'O',

			// Decompositions for Latin Extended-A
			chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
			chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
			chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
			chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
			chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
			chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
			chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
			chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
			chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
			chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
			chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
			chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
			chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
			chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
			chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
			chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
			chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
			chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
			chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
			chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
			chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
			chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
			chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
			chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
			chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
			chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
			chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
			chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
			chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
			chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
			chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
			chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
			chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
			chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
			chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
			chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
			chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
			chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
			chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
			chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
			chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
			chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
			chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
			chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
			chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
			chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
			chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
			chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
			chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
			chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
			chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
			chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
			chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
			chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
			chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
			chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
			chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
			chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
			chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
			chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
			chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
			chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
			chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
			chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',

			// Decompositions for Latin Extended-B
			chr(200) . chr(152) => 'S', chr(200) . chr(153) => 's',
			chr(200) . chr(154) => 'T', chr(200) . chr(155) => 't',

			// Euro Sign
			chr(226) . chr(130) . chr(172) => 'E',

			// GBP (Pound) Sign
			chr(194) . chr(163) => '',

			// Vowels with diacritic (Vietnamese)
			// unmarked
			chr(198) . chr(160) => 'O', chr(198) . chr(161) => 'o',
			chr(198) . chr(175) => 'U', chr(198) . chr(176) => 'u',

			// grave accent
			chr(225) . chr(186) . chr(166) => 'A', chr(225) . chr(186) . chr(167) => 'a',
			chr(225) . chr(186) . chr(176) => 'A', chr(225) . chr(186) . chr(177) => 'a',
			chr(225) . chr(187) . chr(128) => 'E', chr(225) . chr(187) . chr(129) => 'e',
			chr(225) . chr(187) . chr(146) => 'O', chr(225) . chr(187) . chr(147) => 'o',
			chr(225) . chr(187) . chr(156) => 'O', chr(225) . chr(187) . chr(157) => 'o',
			chr(225) . chr(187) . chr(170) => 'U', chr(225) . chr(187) . chr(171) => 'u',
			chr(225) . chr(187) . chr(178) => 'Y', chr(225) . chr(187) . chr(179) => 'y',

			// hook
			chr(225) . chr(186) . chr(162) => 'A', chr(225) . chr(186) . chr(163) => 'a',
			chr(225) . chr(186) . chr(168) => 'A', chr(225) . chr(186) . chr(169) => 'a',
			chr(225) . chr(186) . chr(178) => 'A', chr(225) . chr(186) . chr(179) => 'a',
			chr(225) . chr(186) . chr(186) => 'E', chr(225) . chr(186) . chr(187) => 'e',
			chr(225) . chr(187) . chr(130) => 'E', chr(225) . chr(187) . chr(131) => 'e',
			chr(225) . chr(187) . chr(136) => 'I', chr(225) . chr(187) . chr(137) => 'i',
			chr(225) . chr(187) . chr(142) => 'O', chr(225) . chr(187) . chr(143) => 'o',
			chr(225) . chr(187) . chr(148) => 'O', chr(225) . chr(187) . chr(149) => 'o',
			chr(225) . chr(187) . chr(158) => 'O', chr(225) . chr(187) . chr(159) => 'o',
			chr(225) . chr(187) . chr(166) => 'U', chr(225) . chr(187) . chr(167) => 'u',
			chr(225) . chr(187) . chr(172) => 'U', chr(225) . chr(187) . chr(173) => 'u',
			chr(225) . chr(187) . chr(182) => 'Y', chr(225) . chr(187) . chr(183) => 'y',

			// tilde
			chr(225) . chr(186) . chr(170) => 'A', chr(225) . chr(186) . chr(171) => 'a',
			chr(225) . chr(186) . chr(180) => 'A', chr(225) . chr(186) . chr(181) => 'a',
			chr(225) . chr(186) . chr(188) => 'E', chr(225) . chr(186) . chr(189) => 'e',
			chr(225) . chr(187) . chr(132) => 'E', chr(225) . chr(187) . chr(133) => 'e',
			chr(225) . chr(187) . chr(150) => 'O', chr(225) . chr(187) . chr(151) => 'o',
			chr(225) . chr(187) . chr(160) => 'O', chr(225) . chr(187) . chr(161) => 'o',
			chr(225) . chr(187) . chr(174) => 'U', chr(225) . chr(187) . chr(175) => 'u',
			chr(225) . chr(187) . chr(184) => 'Y', chr(225) . chr(187) . chr(185) => 'y',

			// acute accent
			chr(225) . chr(186) . chr(164) => 'A', chr(225) . chr(186) . chr(165) => 'a',
			chr(225) . chr(186) . chr(174) => 'A', chr(225) . chr(186) . chr(175) => 'a',
			chr(225) . chr(186) . chr(190) => 'E', chr(225) . chr(186) . chr(191) => 'e',
			chr(225) . chr(187) . chr(144) => 'O', chr(225) . chr(187) . chr(145) => 'o',
			chr(225) . chr(187) . chr(154) => 'O', chr(225) . chr(187) . chr(155) => 'o',
			chr(225) . chr(187) . chr(168) => 'U', chr(225) . chr(187) . chr(169) => 'u',

			// dot below
			chr(225) . chr(186) . chr(160) => 'A', chr(225) . chr(186) . chr(161) => 'a',
			chr(225) . chr(186) . chr(172) => 'A', chr(225) . chr(186) . chr(173) => 'a',
			chr(225) . chr(186) . chr(182) => 'A', chr(225) . chr(186) . chr(183) => 'a',
			chr(225) . chr(186) . chr(184) => 'E', chr(225) . chr(186) . chr(185) => 'e',
			chr(225) . chr(187) . chr(134) => 'E', chr(225) . chr(187) . chr(135) => 'e',
			chr(225) . chr(187) . chr(138) => 'I', chr(225) . chr(187) . chr(139) => 'i',
			chr(225) . chr(187) . chr(140) => 'O', chr(225) . chr(187) . chr(141) => 'o',
			chr(225) . chr(187) . chr(152) => 'O', chr(225) . chr(187) . chr(153) => 'o',
			chr(225) . chr(187) . chr(162) => 'O', chr(225) . chr(187) . chr(163) => 'o',
			chr(225) . chr(187) . chr(164) => 'U', chr(225) . chr(187) . chr(165) => 'u',
			chr(225) . chr(187) . chr(176) => 'U', chr(225) . chr(187) . chr(177) => 'u',
			chr(225) . chr(187) . chr(180) => 'Y', chr(225) . chr(187) . chr(181) => 'y',

			// Vowels with diacritic (Chinese, Hanyu Pinyin)
			chr(201) . chr(145) => 'a',

			// macron
			chr(199) . chr(149) => 'U', chr(199) . chr(150) => 'u',
			
			// acute accent
			chr(199) . chr(151) => 'U', chr(199) . chr(152) => 'u',
			
			// caron
			chr(199) . chr(141) => 'A', chr(199) . chr(142) => 'a',
			chr(199) . chr(143) => 'I', chr(199) . chr(144) => 'i',
			chr(199) . chr(145) => 'O', chr(199) . chr(146) => 'o',
			chr(199) . chr(147) => 'U', chr(199) . chr(148) => 'u',
			chr(199) . chr(153) => 'U', chr(199) . chr(154) => 'u',
			
			// grave accent
			chr(199) . chr(155) => 'U', chr(199) . chr(156) => 'u',
		);

		$sString = strtr($sString, $chars);
	}
	else
	{
		// Assume ISO-8859-1 if not UTF-8
		$chars['in'] =
			chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158)
			. chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194)
			. chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202)
			. chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210)
			. chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218)
			. chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227)
			. chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235)
			. chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243)
			. chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251)
			. chr(252) . chr(253) . chr(255);

		$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

		$sString = strtr($sString, $chars['in'], $chars['out']);
		$double_chars['in'] = array(
			chr(140), chr(156), chr(198), chr(208), chr(222),
			chr(223), chr(230), chr(240), chr(254)
		);
		$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
		$sString = str_replace($double_chars['in'], $double_chars['out'], $sString);
	}

	return $sString;
}

/**
 * Make a random password with the given length.
 * 
 * @param int $iLen Desired password length.
 * @return string
 */
function makePassword(int $iLen): string
{
	$sChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_[]{}<>~`+=,.;:/?|';
	$iCharsLen = strlen($sChars) - 1;
	$sPwd = '';

	for ($i = 0; $i < $iLen; $i++)
		$sPwd .= $sChars[random_int(0, $iCharsLen)];

	return $sPwd;
}

/**
 * Make a random private key for the given cipher method.
 * 
 * @see openssl_get_cipher_methods() to get available methods.
 * @param string $sCipher Cipher method or keep empty for default (AES-256-CTR).
 * @return string
 */
function makePrivateKey(string $sCipher = 'aes-256-ctr'): string
{
	$iLen = openssl_cipher_key_length($sCipher);
	return makePassword(openssl_cipher_key_length($sCipher));
}

/**
 * Encrypt a string.
 * 
 * Result is the Base64 encoding of the next sequence :
 * 
 * "IV" + Control hash + Encrypted data
 * 
 * - IV length depends on `$sCipher` used (use `openssl_cipher_iv_length()`),
 * - Control hash length is 32.
 * 
 * @param string $sData String to encrypt.
 * @param string $sPrivateKey Private key needed to encrypt.
 * @param string $sCipher Cipher method with which encrypt.
 * @return string Encrypted string, Base64 encoded.
 * @throws LengthException in case of error with retreiving IV length.
 */
function encrypt(string $sData, string $sPrivateKey, string $sCipher = 'aes-256-ctr'): string
{
	$iIvLen = openssl_cipher_iv_length($sCipher);

	if ($iIvLen === false)
		throw new LengthException('Unable to retreive cipher IV length');

	$sIV = openssl_random_pseudo_bytes($iIvLen);
	$sEncryptedData = openssl_encrypt($sData, $sCipher, $sPrivateKey, OPENSSL_RAW_DATA, $sIV);
	$sEncryptedHash = hash_hmac('sha3-256', $sEncryptedData, $sPrivateKey, true /* binary */);
	return base64_encode($sIV . $sEncryptedHash . $sEncryptedData);
}

/**
 * Decrypt a string encrypted with `encrypt()`.
 * 
 * @param string $sData Encrypted data.
 * @param string $sPrivateKey Same private key used to encrypt.
 * @param string $sCipher Same cipher method used to encrypt.
 * @return string Decrypted string.
 * @throws LengthException in case of error with retreiving IV length.
 * @throws UnexpectedValueException if hash control mismatch.
 */
function decrypt(string $sData, string $sPrivateKey, string $sCipher = 'aes-256-ctr'): string
{
	$sRawData = base64_decode($sData);

	$iIvLen = openssl_cipher_iv_length($sCipher);

	if ($iIvLen === false)
		throw new LengthException('Unable to retreive cipher IV length');

	$sIV = substr($sRawData, 0, $iIvLen);
	$sEncryptedHash = substr($sRawData, $iIvLen, $iHashLen = 32);
	$sEncryptedData = substr($sRawData, $iIvLen + $iHashLen);

	$sPayload = openssl_decrypt($sEncryptedData, $sCipher, $sPrivateKey, OPENSSL_RAW_DATA, $sIV);
	$sControlHash = hash_hmac('sha3-256', $sEncryptedData, $sPrivateKey, true /* binary */);
	
	if (hash_equals($sEncryptedHash, $sControlHash))
	{
		return $sPayload;
	}

	throw new UnexpectedValueException('Hash control mismatch');
}

if (!function_exists('openssl_cipher_key_length')) :

	/**
	 * Polyfill to openssl_cipher_key_length() function for PHP < 8.2.0.
	 * 
	 * @see PHP documentation.
	 */
	function openssl_cipher_key_length(string $cipher_algo): int|false
	{
		$length = match (strtolower($cipher_algo))
		{
			'aes-128-cbc' => 16,
			'aes-128-cbc-hmac-sha1' => 16,
			'aes-128-cbc-hmac-sha256' => 16,
			'aes-128-ccm' => 16,
			'aes-128-cfb' => 16,
			'aes-128-cfb1' => 16,
			'aes-128-cfb8' => 16,
			'aes-128-ctr' => 16,
			'aes-128-ecb' => 16,
			'aes-128-gcm' => 16,
			'aes-128-ocb' => 16,
			'aes-128-ofb' => 16,
			'aes-128-wrap' => 16,
			'aes-128-wrap-pad' => 16,
			'aes-128-xts' => 32,
			'aes-192-cbc' => 24,
			'aes-192-ccm' => 24,
			'aes-192-cfb' => 24,
			'aes-192-cfb1' => 24,
			'aes-192-cfb8' => 24,
			'aes-192-ctr' => 24,
			'aes-192-ecb' => 24,
			'aes-192-gcm' => 24,
			'aes-192-ocb' => 24,
			'aes-192-ofb' => 24,
			'aes-192-wrap' => 24,
			'aes-192-wrap-pad' => 24,
			'aes-256-cbc' => 32,
			'aes-256-cbc-hmac-sha1' => 32,
			'aes-256-cbc-hmac-sha256' => 32,
			'aes-256-ccm' => 32,
			'aes-256-cfb' => 32,
			'aes-256-cfb1' => 32,
			'aes-256-cfb8' => 32,
			'aes-256-ctr' => 32,
			'aes-256-ecb' => 32,
			'aes-256-gcm' => 32,
			'aes-256-ocb' => 32,
			'aes-256-ofb' => 32,
			'aes-256-wrap' => 32,
			'aes-256-wrap-pad' => 32,
			'aes-256-xts' => 64,
			'aria-128-cbc' => 16,
			'aria-128-ccm' => 16,
			'aria-128-cfb' => 16,
			'aria-128-cfb1' => 16,
			'aria-128-cfb8' => 16,
			'aria-128-ctr' => 16,
			'aria-128-ecb' => 16,
			'aria-128-gcm' => 16,
			'aria-128-ofb' => 16,
			'aria-192-cbc' => 24,
			'aria-192-ccm' => 24,
			'aria-192-cfb' => 24,
			'aria-192-cfb1' => 24,
			'aria-192-cfb8' => 24,
			'aria-192-ctr' => 24,
			'aria-192-ecb' => 24,
			'aria-192-gcm' => 24,
			'aria-192-ofb' => 24,
			'aria-256-cbc' => 32,
			'aria-256-ccm' => 32,
			'aria-256-cfb' => 32,
			'aria-256-cfb1' => 32,
			'aria-256-cfb8' => 32,
			'aria-256-ctr' => 32,
			'aria-256-ecb' => 32,
			'aria-256-gcm' => 32,
			'aria-256-ofb' => 32,
			'camellia-128-cbc' => 16,
			'camellia-128-cfb' => 16,
			'camellia-128-cfb1' => 16,
			'camellia-128-cfb8' => 16,
			'camellia-128-ctr' => 16,
			'camellia-128-ecb' => 16,
			'camellia-128-ofb' => 16,
			'camellia-192-cbc' => 24,
			'camellia-192-cfb' => 24,
			'camellia-192-cfb1' => 24,
			'camellia-192-cfb8' => 24,
			'camellia-192-ctr' => 24,
			'camellia-192-ecb' => 24,
			'camellia-192-ofb' => 24,
			'camellia-256-cbc' => 32,
			'camellia-256-cfb' => 32,
			'camellia-256-cfb1' => 32,
			'camellia-256-cfb8' => 32,
			'camellia-256-ctr' => 32,
			'camellia-256-ecb' => 32,
			'camellia-256-ofb' => 32,
			'chacha20' => 32,
			'chacha20-poly1305' => 32,
			'des-ede-cbc' => 16,
			'des-ede-cfb' => 16,
			'des-ede-ecb' => 16,
			'des-ede-ofb' => 16,
			'des-ede3-cbc' => 24,
			'des-ede3-cfb' => 24,
			'des-ede3-cfb1' => 24,
			'des-ede3-cfb8' => 24,
			'des-ede3-ecb' => 24,
			'des-ede3-ofb' => 24,
			'des3-wrap' => 24,
			'sm4-cbc' => 16,
			'sm4-cfb' => 16,
			'sm4-ctr' => 16,
			'sm4-ecb' => 16,
			'sm4-ofb' => 16,
			default => false,
		};

		if ($length === false)
		{
			trigger_error('openssl_cipher_key_length(): Unknown cipher algorithm', E_USER_WARNING);
		}

		return $length;
	}
	
endif;
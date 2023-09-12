<?php

test('makePassword', function()
{
	expect(strlen(makePassword(8)))->toBe(8);
});

test('makeCipherPrivateKey', function ()
{
	expect(strlen(makePrivateKey('aes-256-ctr')))->toBe(32);
});

test('encryption', function()
{
	$data = 'This string must be encrypted and decrypted';
	$key = makePrivateKey();

	expect(decrypt(encrypt($data, $key), $key))->toBe($data);
});

test('decryption error', function()
{
	$data = 'This string must be encrypted and decrypted';
	$key = makePrivateKey();

	$encrypted = encrypt($data, $key);
	$encrypted[random_int(0, strlen($encrypted) - 1)] = 0;

	decrypt($encrypted, $key);
})
->throws(UnexpectedValueException::class);

test('encryption cipher error', function()
{
	encrypt('example', makePrivateKey(), 'aes-256-abc');
})
->throws(LengthException::class);
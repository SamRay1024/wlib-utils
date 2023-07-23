<?php

test('ckdir', function()
{
	expect(ckdir(__DIR__.'/ckdir'))->toBeTrue();
	expect(__DIR__.'/ckdir')->toBeReadableDirectory();
	expect(ckdir(__DIR__.'/ckdir'))->toBeTrue();
	rmdir(__DIR__ . '/ckdir');
});
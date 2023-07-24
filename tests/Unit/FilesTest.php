<?php

test('createDir', function()
{
	expect(createDir(__DIR__.'/testdir'))->toBeTrue();
	expect(__DIR__. '/testdir')->toBeReadableDirectory();
	expect(createDir(__DIR__.'/testdir'))->toBeTrue();
	rmdir(__DIR__ . '/testdir');
});
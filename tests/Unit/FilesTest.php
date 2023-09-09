<?php

test('createDir', function()
{
	expect(createDir(__DIR__ .'/testdir', 0755))->toBeTrue();
	expect(__DIR__ .'/testdir')->toBeReadableDirectory();
	expect(createDir(__DIR__ .'/testdir'))->toBeTrue();
});

test('makeCanonical', function()
{
	expect(makeCanonical('/this/is/a/test'))->toBe('/this/is/a/test/');
	expect(makeCanonical('/this/is/a/test/'))->toBe('/this/is/a/test/');
});

test('fileGetContent', function()
{
	expect(fileGetContent(__DIR__))->toBeFalse();
	expect(fileGetContent(__DIR__ .'/config/b.php'))
		->toBe('<?php // Config file which does not return an array');
});

test('fileGetBirthTime', function()
{
	expect(fileGetBirthTime(__FILE__))->toBeInt();
	expect(fileGetBirthTime(__DIR__))->toBeInt();
});

test('filePutContent', function()
{
	expect(filePutContent(__DIR__ .'/testdir/testfile.txt', 'content', 0755))
		->toBe(7);
});

test('emptyDir', function()
{
	expect(emptyDir(__DIR__ .'/testdir'))
		->toBeTrue();

	expect(file_exists(__DIR__ .'/testdir/testfile.txt'))
		->toBeFalse();
});

test('removeDir', function()
{
	expect(removeDir(__DIR__ .'/testdir'))
		->toBeTrue();

	expect(is_dir(__DIR__ .'/testdir'))->toBeFalse();
});

test('walkDir', function()
{
	$files = [];
	$closure = function (&$item, $skip_hidden_files) use (&$files)
	{
		if ($skip_hidden_files && basename($item)[0] == '.')
			return;

		$files[] = str_replace(__DIR__ . '/', '', (string) $item);
	};

	walkDir(__DIR__, $closure, ['skip_hidden_files' => true], true);

	sort($files);
	
	expect($files)->toMatchArray([
		'ArraysTest.php', 'FilesTest.php', 'StringsTest.php',
		'config/a.php', 'config/b.php'
	]);
});
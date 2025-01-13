<?php

$array = [
    'foo' => 'bar',
    'bar' => 'baz',
    1 => 2,
    'bim' => [
        'bam' => 'boom'
    ]
];

class Obj implements ArrayAccess
{
    private $container = array();

    public function __construct($array)
    {
        $this->container = $array;
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset))
            $this->container[] = $value;
        else
            $this->container[$offset] = $value;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}

test('isArrayKey » array', function () use ($array)
{
    expect(isArrayKey($array, 'bar'))->toBeTrue();
    expect(isArrayKey($array, 'key'))->toBeFalse();
    expect(isArrayKey($array, 1))->toBeTrue();
    expect(isArrayKey($array, 'bim'))->toBeTrue();
    expect(isArrayKey($array, 'bim.bam'))->toBeTrue();
    expect(isArrayKey($array, 'bim.boom'))->toBeFalse();
    expect(isArrayKey($array, 'boom.bim'))->toBeFalse();
});

test('isArrayKey » ArrayAccess', function() use ($array)
{
    $obj = new Obj($array);

    expect(isArrayKey($obj, 'bar'))->toBeTrue();
    expect(isArrayKey($obj, 'key'))->toBeFalse();
    expect(isArrayKey($obj, 1))->toBeTrue();
});

test('arrayValue » array', function() use ($array)
{
    expect(arrayValue($array, 'foo'))->toBe('bar');
    expect(arrayValue($array, 1))->toBe(2);
    expect(arrayValue($array, 'baz'))->toBeNull();
    expect(arrayValue($array, 'baz', 'bam'))->toBe('bam');
});

test('arrayValue » ArrayAccess', function() use ($array)
{
    $obj = new Obj($array);

    expect(arrayValue($obj, 'foo'))->toBe('bar');
    expect(arrayValue($obj, 1))->toBe(2);
    expect(arrayValue($obj, 'baz'))->toBeNull();
    expect(arrayValue($obj, 'baz', 'bam'))->toBe('bam');
});

test('arrayExtend', function() use ($array)
{
    $copy = $array;
    $array2 = ['bam' => 'bim', 1 => 4];
    arrayExtend($copy, $array2);

    expect(count($copy))->toBe(5);
    expect($copy['foo'])->toBe('bar');
    expect($copy['bam'])->toBe('bim');
    expect($copy['1'])->toBe(4);
    expect(arrayValue($copy, 2))->toBeNull();
});

$array['root'] = [
    'item1' => 'value1',
    'item2' => [
        'subitem1' => 'subvalue1'
    ]
];

test('access » read', function() use ($array)
{
    expect(access($array, 'foo'))->toBe('bar');
    expect(access($array, 1))->toBe(2);
    expect(access($array, 'baz'))->toBeNull();
    expect(access($array, 'baz', 'bam'))->toBe('bam');
    expect(access($array, 'root.item1'))->toBe('value1');
    expect(access($array, 'root.item2.subitem1'))->toBe('subvalue1');
    expect(access($array, 'root.item3', 'value3'))->toBe('value3');
});

test('access » write', function() use ($array)
{
    access($array, ['root.item3' => ['subitem2' => 'subvalue2']]);

    expect(access($array, 'root.item3.subitem2'))->toBe('subvalue2');
});

test('globals » read', function()
{
    expect(globals())->toBeArray();
    expect(globals('argv')[0])->toStartWith('./vendor/bin/pest');
    expect(globals('_SERVER.SCRIPT_NAME'))->toStartWith('./vendor/bin/pest');
    expect(globals('unknown'))->toBeNull();
    expect(globals('unknown', true))->toBeTrue();
});

test('globals » write', function()
{
    globals(['foo' => 'bar']);
    global $foo;
    expect($foo)->toBe('bar');

    globals(['bar.baz' => 'bam']);
    global $bar;
    expect($bar['baz'])->toBe('bam');
});

test('server » read', function()
{
    expect(server('SCRIPT_NAME'))->toStartWith('./vendor/bin/pest');
    expect(server('unknown'))->toBeNull();
    expect(server('unknown', true))->toBeTrue();
});

test('server » write', function()
{
    server(['foo.bar' => 'baz']);
    expect($_SERVER['foo']['bar'])->toBe('baz');
});

test('get » read', function()
{
    $_GET['foo'] = 'bar';
    expect(get('foo'))->toBe('bar');

    $_GET['bar']['baz'] = 'bam';
    expect(get('bar.baz'))->toBe('bam');

    expect(get('unknown'))->toBeNull();
    expect(get('unknown', true))->toBeTrue();
});

test('get » write', function()
{
    get(['a.b' => 'c']);
    expect($_GET['a']['b'])->toBe('c');
});

test('post » read', function ()
{
    $_POST['foo'] = 'bar';
    expect(post('foo'))->toBe('bar');

    $_POST['bar']['baz'] = 'bam';
    expect(post('bar.baz'))->toBe('bam');

    expect(post('unknown'))->toBeNull();
    expect(post('unknown', true))->toBeTrue();
});

test('post » write', function ()
{
    post(['a.b' => 'c']);
    expect($_POST['a']['b'])->toBe('c');
});

test('files » read', function ()
{
    $_FILES['foo'] = 'bar';
    expect(files('foo'))->toBe('bar');

    $_FILES['bar']['baz'] = 'bam';
    expect(files('bar.baz'))->toBe('bam');

    expect(files('unknown'))->toBeNull();
    expect(files('unknown', true))->toBeTrue();
});

test('files » write', function ()
{
    files(['a.b' => 'c']);
    expect($_FILES['a']['b'])->toBe('c');
});

test('cookie » read only', function()
{
    // Attention : en condition réelle, un cookie doit être créé via `setcookie()`
    $_COOKIE['TestCookie'] = 'Cookie value';
    expect(cookie('TestCookie'))->toBe('Cookie value');
    expect(cookie('CookieTest'))->toBeNull();
    expect(cookie('CookieTest', 'Default'))->toBe('Default');
});

test('session » write', function()
{
    session_start();
    session([
        'key1' => 'value1',
        'a.b.c' => 'd'
    ]);
    expect($_SESSION['key1'])->toBe('value1');
    expect($_SESSION['a']['b']['c'])->toBe('d');
});

test('session » read', function()
{
    expect(session('key1'))->toBe('value1');
    expect(session('a.b.c'))->toBe('d');
    expect(session('key2'))->toBeNull();
    expect(session('key2', 'value2'))->toBe('value2');
});

test('request » write', function ()
{
    request([
        'key1' => 'value1',
        'a.b.c' => 'd'
    ]);
    expect($_REQUEST['key1'])->toBe('value1');
    expect($_REQUEST['a']['b']['c'])->toBe('d');
});

test('request » read', function ()
{
    expect(request('key1'))->toBe('value1');
    expect(request('a.b.c'))->toBe('d');
    expect(request('key2'))->toBeNull();
    expect(request('key2', 'value2'))->toBe('value2');
});

test('env » write', function ()
{
    env([
        'key1' => 'value1',
        'a.b.c' => 'd'
    ]);
    expect($_ENV['key1'])->toBe('value1');
    expect($_ENV['a']['b']['c'])->toBe('d');
});

test('env » read', function ()
{
    expect(env('key1'))->toBe('value1');
    expect(env('a.b.c'))->toBe('d');
    expect(env('key2'))->toBeNull();
    expect(env('key2', 'value2'))->toBe('value2');
});

test('config » not set', function ()
{
    expect(config('a.param', 'default'))->toBe('default');
});

test('config » config value error', function ()
{
    global $_CONFIG;
    $_CONFIG['__include_paths'] = true;

    config('something');
})
->throws(UnexpectedValueException::class);

test('config » config include path error', function ()
{
    global $_CONFIG;
    $_CONFIG['__include_paths'] = __DIR__ .'/confg/';

    config('something');
})
->throws(UnexpectedValueException::class);

test('config » a » good config file', function ()
{
    global $_CONFIG;
    $_CONFIG['__include_paths'] = __DIR__ .'/config/';

    expect(config('a.param'))->toBe('value');
});

test('config » b » bad config file', function ()
{
    config('b.param');
})
->throws(LogicException::class);

test('unaccess', function() use ($array)
{
    unaccess($array, 'bar');
    unaccess($array, 1);

    expect(isArrayKey($array, 'bar'))->toBeFalse();
    expect(isArrayKey($array, 1))->toBeFalse();

    unaccess($array, 'root.item2.subitem1');
    expect($array['root']['item2'])->toBeArray()->toHaveCount(0);
});

test('unglobals', function()
{
    unglobals('foo', 'bar.baz');

    expect(array_key_exists('foo', $GLOBALS))->toBeFalse();
    expect($GLOBALS['bar'])->toBeArray()->toHaveCount(0);

    globals(['a.b.c' => 'c', 'a.b.d' => 'd']);
    unglobals('a.b.c');
    expect(globals('a.b.c'))->toBeNull();
    expect(globals('a.b.d'))->toBe('d');
});

test('unserver', function ()
{
    unserver('foo.bar');
    expect($_SERVER['foo'])->toBeArray()->toHaveCount(0);
});

test('unget', function ()
{
    unget('foo', 'bar');
    expect(get('foo'))->toBeNull();
    expect(get('bar'))->toBeNull();
});

test('unpost', function ()
{
    unpost('foo', 'bar');
    expect(post('foo'))->toBeNull();
    expect(post('bar'))->toBeNull();
});

test('unfiles', function ()
{
    unfiles('foo', 'bar');
    expect(files('foo'))->toBeNull();
    expect(files('bar'))->toBeNull();
});

test('uncookie', function ()
{
    uncookie('TestCookie');
    expect(cookie('TestCookie'))->toBeNull();
});

test('unsession', function ()
{
    unsession('key1', 'a.b');
    expect(session('key1'))->toBeNull();
    expect($_SESSION['a'])->toBeArray()->toHaveCount(0);
});

test('unrequest', function ()
{
    unrequest('key1', 'a.b');
    expect(request('key1'))->toBeNull();
    expect($_REQUEST['a'])->toBeArray()->toHaveCount(0);
});

test('unenv', function ()
{
    unenv('key1', 'a.b');
    expect(env('key1'))->toBeNull();
    expect($_ENV['a'])->toBeArray()->toHaveCount(0);
});
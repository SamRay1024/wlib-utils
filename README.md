# wlib/utils

Fonctions PHP utilitaires.

## Installation

```shell
// Utilisation en production
composer require --no-dev wlib/utils

// Utilisation en développement
composer require --dev wlib/utils
```

## Sucreries disponibles

### Tableaux

#### Fonctions génériques

```php
// Vérifier la présence d'une clé dans un tableau ou une instance de ArrayAccess
function isArrayKey(array|ArrayAccess $aArray, int|string $mKey): bool;

// Accéder à une valeur dans un tableau ou retourner une valeur par défaut
function arrayValue(array|ArrayAccess &$aArray, int|string $mKey, mixed $mDefault = null): mixed;

// Etendre un tableau à la façon jQuery.extend()
function arrayExtend(array &$aTarget, array ...$aArrays): void;
```

#### Accesseurs génériques

```php
function access(array &$aArray, string|array $mName = '', mixed $mDefault = null): mixed;
function unaccess(array &$aCursor, string|int ...$mKey): void;
```

`access()` et `unaccess()` sont des accesseurs génériques qui permettent de lire, écrire et retirer des éléments dans des tableaux multidimentionnels de façon simplifiée.

Ils permettent notamment d'économiser les contrôles incessants sur l'existence des clés avant d'y accéder :

```php
// Accès basique
access($array, 'keyname');
access($array, 'keyname', 'default value if keyname is not set');

// Accès multidimentionnel
access($array, 'level1.level2.level3');
// Similaire à $array['level1']['level2']['level3']...sans avoir besoin de vérifier l'existence de chaque dimension

// Ecrire un élément (ajout ou mise à jour)
access($array, ['keyname' => 'keyvalue']);

// Ecrire plusieurs éléments
access($array, [
	'key1' => 'value1',	// Similaire à $array['key1'] = 'value1';
	'key2' => 'value2',
	'a.b.c' => 'value3'	// Similaire à $array['a']['b']['c'] = 'value3';
]);

// Retirer des éléments
unaccess($array, 'key1');					// Similaire à unset($array['key1'])
unaccess($array, 'a.b.c');					// Similaire à unset($array['a']['b']['c']);
unaccess($array, 'key2', 'key3', 'a.b.c');	// Similaire à unset($array['key2'], $array['key3'], $array['a']['b']['c']);
```

#### Accès aux superglobales

Maintenant que vous connaissez `access()` et `unaccess()`, vous pouvez accédez aux superglobales de PHP de la même façon :

```php
// Encapsulation d'access()
function globals(string|array $mName = '', mixed $mDefault = null): mixed;
function server(string|array $mName = '', mixed $mDefault = null): mixed;
function get(string|array $mName = '', mixed $mDefault = null): mixed;
function post(string|array $mName = '', mixed $mDefault = null): mixed;
function files(string|array $mName = '', mixed $mDefault = null): mixed;
function cookie(string $sName = '', mixed $mDefault = null): mixed;
function session(string|array $mName = '', mixed $mDefault = null): mixed;
function request(string|array $mName = '', mixed $mDefault = null): mixed;
function env(string|array $mName = '', mixed $mDefault = null): mixed;

// Encapsulations d'unaccess()
function unglobals(string|int $mKey): void;
function unserver(string|int $mKey): void;
function unget(string|int $mKey): void;
function unpost(string|int $mKey): void;
function unfiles(string|int $mKey): void;
function uncookie(string|int $mKey): void;
function unsession(string|int $mKey): void;
function unrequest(string|int $mKey): void;
function unenv(string|int $mKey): void;
```

#### `config()`

```php
function config(string $sName = '', mixed $mDefault = null): mixed;
```

Cette fonction, qui bénéficie également des petites douceurs proposées par `access()`, est un outil qui propose de structurer des tableaux contenants, par exemple, les valeurs de configuration de votre application :

```php
// Prérequis, définir le(s) dossier(s) où vont se trouver des fichiers de configuration
$_CONFIG['__include_paths'] = __DIR__.'/cfg';

// Fichier __DIR__.'/cfg/app.php'
return [
	'title'	=> 'My awesome app',
	'locale' => [
		'timezone' => 'Europe/Paris'
	]
]

// Le premier terme de $sName correspond au nom du fichier auquel accéder
echo config('app.title', 'Define your title app'); // > My awesome app
date_default_timezone_set(config('app.locale.timezone'));
```

### Déboguage

```php
// Dumper les arguments donnés et continuer l'exécution
function vd(...$var): void;

// Dumper les arguments donnés et arrêter l'exécution
function vdd(...$var): void;
```

### Fichiers

```php
// Créer le dossier à l'adresse donnée s'il n'existe pas déjà
function ckdir($sDirName, $iMode = 0644): bool;
```

### Chaînes de caractères

```php
// Vérifier si la chaîne donnée est au format UTF-8
function isUTF8($sString): bool;

// Retirer les accents de la chaîne donnée
function removeAccents($sString): string;
```

_Ces deux fonctions proviennent de la base de code de WordPress !_

#### Chiffrement

```php
// Générer un mot de passe aléatoire de la longueur donnée
function makePassword(int $iLen): string;

// Générer une clé privée de la longueur attendue par l'algorythme donné
function makePrivateKey(string $sCipher = 'aes-256-ctr'): string;

// Crypter une chaîne
function encrypt(string $sData, string $sPrivateKey, string $sCipher = 'aes-256-ctr'): string;

// Décrypter une chaine
function decrypt(string $sData, string $sPrivateKey, string $sCipher = 'aes-256-ctr'): string;
```

Exemple :

```php
$key = makePrivateKey();
$data = 'Some content to protect';

$enc = encrypt($data, $key);

if (decrypt($enc, $key) == $data)
	echo 'All is under control !';
```
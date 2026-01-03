Simple PHP utility classes for handling filesystem paths, permissions, and timestamps.

---

## Installation

Install via Composer:

```bash
composer require guillaumetissier/path-utilities
````

---

## Usage

```php
use Guillaumetissier\PathUtilities\Path;

$path = new Path('/path/to/file.txt');

// Basic information
echo $path->basename();  // file.txt
echo $path->extension(); // txt
echo $path->dirname();   // /path/to

// Parent directory
$parent = $path->parent();

// Check existence and type
if ($path->exists() && $path->isFile()) {
    echo "File exists!";
}

// File size
echo $path->size(); // in bytes

// Permissions
$permissions = $path->permissions();
echo $permissions->octal();     // e.g., 0644
echo $permissions->symbolic();  // e.g., rw-r--r--

// File times
$times = $path->times();
echo $times->modification()->format('Y-m-d H:i:s');
```

---

## Features

* Path utilities (`basename`, `dirname`, `filename`, `extension`, `parent`)
* File existence and type checks (`isFile`, `isDir`, `isLink`)
* File size
* Permissions handling via `PathPermissions`
* File timestamps via `PathTimes`
* Optional exception mode for filesystem warnings

---

## Requirements

* PHP 8.1 or higher
* Composer

---

## Development

Install development dependencies:

```bash
composer install
```

Run tests:

```bash
composer test
```

Fix coding style:

```bash
composer cs
```

---

## License

MIT License

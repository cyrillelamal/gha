<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitaa475372d1afb7f112bf50e9b8859e3a
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LifterLMS\\CLI\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LifterLMS\\CLI\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitaa475372d1afb7f112bf50e9b8859e3a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitaa475372d1afb7f112bf50e9b8859e3a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitaa475372d1afb7f112bf50e9b8859e3a::$classMap;

        }, null, ClassLoader::class);
    }
}

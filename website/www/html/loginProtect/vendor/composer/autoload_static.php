<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit45dbaad747772e2d65a422b687c95137
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WhiteHat101\\Crypt\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WhiteHat101\\Crypt\\' => 
        array (
            0 => __DIR__ . '/..' . '/whitehat101/apr1-md5/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit45dbaad747772e2d65a422b687c95137::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit45dbaad747772e2d65a422b687c95137::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit45dbaad747772e2d65a422b687c95137::$classMap;

        }, null, ClassLoader::class);
    }
}
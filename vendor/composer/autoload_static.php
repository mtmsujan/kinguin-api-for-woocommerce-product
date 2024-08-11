<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit70a4ea812c02abcf296075f6aca96f3a
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Automattic\\WooCommerce\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Automattic\\WooCommerce\\' => 
        array (
            0 => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit70a4ea812c02abcf296075f6aca96f3a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit70a4ea812c02abcf296075f6aca96f3a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit70a4ea812c02abcf296075f6aca96f3a::$classMap;

        }, null, ClassLoader::class);
    }
}

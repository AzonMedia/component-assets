<?php
declare(strict_types=1);

namespace GuzabaPlatform\Assets;

use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use GuzabaPlatform\Installer\Installer;
use GuzabaPlatform\Installer\Interfaces\PostInstallHookInterface;

class PostInstall implements PostInstallHookInterface
{
    public static function post_install_hook(Installer $Installer, InstalledRepositoryInterface $Repo, PackageInterface $Package) : void
    {
        $guzaba_platform_dir = $Installer->getInstallPath($Package);
        //$private_assets_dir = $guzaba_platform_dir . '/app/assets';
        $public_assets_dir = $guzaba_platform_dir . '/app/public/assets';
        print 'Creating public assets dir: '.$public_assets_dir.PHP_EOL;
//        if (!file_exists($private_assets_dir)) {
//            mkdir($private_assets_dir);
//        }
        if (!file_exists($public_assets_dir)) {
            mkdir($public_assets_dir);
        }
    }
}

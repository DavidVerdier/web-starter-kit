<?php

namespace App\Twig\Extension;

use Symfony\Component\Asset\Package;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    private $package;

    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('asset', array($this, 'getAssetUrl')),
        );
    }

    /**
     * Returns the public url/path of an asset.
     *
     * If the package used to generate the path is an instance of
     * UrlPackage, you will always get a URL and not a path.
     *
     * @param string $path        A public path
     *
     * @return string The public path of the asset
     */
    public function getAssetUrl($path)
    {
        return $this->package->getUrl($path);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'asset';
    }
}

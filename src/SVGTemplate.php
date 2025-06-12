<?php

namespace StevieMayhew\SilverStripeSVG;

use DOMDocument;
use Psr\Log\LoggerInterface;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Model\ModelData;
use SilverStripe\ORM\FieldType\DBField;

/**
 * Class SVGTemplate
 */
class SVGTemplate extends ModelData
{
    /**
     * The base path to your SVG location
     *
     * @config
     * @var string
     */
    private static $base_path = 'mysite/svg/';

    /**
     * @config
     * @var string
     */
    private static $extension = 'svg';

    /**
     * @config
     * @var array
     */
    private static $default_extra_classes = [];

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $fill;

    /**
     * @var string
     */
    private $stroke;

    /**
     * @var string
     */
    private $width;

    /**
     * @var string
     */
    private $height;

    /**
     * @var string
     */
    private $customBasePath;

    /**
     * @var array
     */
    private $extraClasses = [];

    /**
     * @var array
     */
    private $subfolders;

    private $name;

    private $id;

    private $out;


    /**
     * @param string $name
     * @param string $id
     */
    public function __construct($name, $id = '')
    {
        $this->name = $name;
        $this->id = $id;
        $this->extraClasses = $this->config()->get('default_extra_classes') ?: [];
        $this->extraClasses[] = 'svg-' . $this->name;
        $this->subfolders = [];
        $this->out = new DOMDocument();
        $this->out->formatOutput = true;
    }

    /**
     * @param $color
     * @return $this
     */
    public function fill($color)
    {
        $this->fill = $color;
        return $this;
    }

    /**
     * @param $color
     * @return $this
     */
    public function stroke($color)
    {
        $this->stroke = $color;
        return $this;
    }

    /**
     * @param $width
     * @return $this
     */
    public function width($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @param $height
     * @return $this
     */
    public function height($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @param $width
     * @param $height
     * @return $this
     */
    public function size($width, $height)
    {
        $this->width($width);
        $this->height($height);
        return $this;
    }

    /**
     * @param $class
     * @return $this
     */
    public function customBasePath($path)
    {
        $this->customBasePath = trim($path, DIRECTORY_SEPARATOR);

        return $this;
    }

    /**
     * @param $class
     * @return $this
     */
    public function extraClass($class)
    {
        $this->extraClasses[] = $class;

        return $this;
    }

    /**
     * @param $class
     * @return $this
     */
    public function addSubfolder($folder)
    {
        $this->subfolders[] = trim($folder, DIRECTORY_SEPARATOR);

        return $this;
    }


    public function isRemoteSvg(): bool
    {
        if ((strpos($this->name, '://') !== false || strpos($this->name, '//') === 0)) {
            return true;
        }

        if (strpos($this->name, 'data:image') === 0) {
            return true;
        }

        return false;
    }


    /**
     * @param $filePath
     * @return string
     */
    private function process($filePath)
    {
        $this->extend('onBeforeProcess', $filePath);

        if (!$this->isRemoteSvg() && !file_exists($filePath)) {
            Injector::inst()->get(LoggerInterface::class)->warning('SVG file not found: ' . $filePath);

            return false;
        }

        if ($this->isRemoteSvg()) {
            // check to see if it exists locally in TEMP_PATH
            $localPath = TEMP_PATH . DIRECTORY_SEPARATOR . md5($filePath) . '.' . $this->config()->get('extension');
            if (file_exists($localPath) && !isset($_GET['flush'])) {
                $filePath = $localPath;
            } else {
                $svg = file_get_contents($filePath);
                file_put_contents($localPath, $svg);
                $filePath = $localPath;
            }
        }

        $out = new DOMDocument();
        $out->load($filePath);

        if (!is_object($out) || !is_object($out->documentElement)) {
            Injector::inst()->get(LoggerInterface::class)->warning('SVG file not loaded: ' . $filePath);
            return false;
        }

        $root = $out->documentElement;
        if ($this->fill) {
            $root->setAttribute('fill', $this->fill);
        }

        if ($this->stroke) {
            $root->setAttribute('stroke', $this->stroke);
        }

        if ($this->width) {
            $root->setAttribute('width', $this->width);
        }

        if ($this->height) {
            $root->setAttribute('height', $this->height);
        }

        if ($this->extraClasses) {
            $root->setAttribute('class', implode(' ', $this->extraClasses));
        }

        foreach ($out->getElementsByTagName('svg') as $element) {
            if ($this->id) {
                $element->setAttribute('id', $this->id);
            } else {
                if ($element->hasAttribute('id')) {
                    $element->removeAttribute('id');
                }
            }
        }

        $out->normalizeDocument();

        return $out->saveHTML();
    }

    /**
     * @return HTMLText
     */
    public function forTemplate()
    {
        // absolute svg
        if ($this->isRemoteSvg()) {
            return DBField::create_field('HTMLText', $this->process($this->name));
        }

        $path = $this->fullSvgPathForTemplate();

        return DBField::create_field('HTMLText', $this->process($path));
    }


    public function fullSvgPathForTemplate()
    {
        $basePath = $this->customBasePath ?? $this->config()->get('base_path');
        $parts = [
            BASE_PATH,
            $basePath
        ];

        foreach ($this->subfolders as $subfolder) {
            $parts[] = $subfolder;
        }

        $extension = $this->config()->get('extension');
        $parts[] = (strpos($this->name, ".") === false) ? $this->name . '.' . $extension : $this->name;
        $path = Controller::join_links(array_filter($parts));

        return $path;
    }
}

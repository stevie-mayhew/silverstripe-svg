<?php

/**
 * Class SVGTemplate
 */
class SVGTemplate extends ViewableData
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
    private static $default_extra_classes = array();

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
    private $width;

    /**
     * @var string
     */
    private $height;

    /**
     * @var array
     */
    private $extraClasses;

    /**
     * @param string $name
     * @param string $id
     */
    public function __construct($name, $id = '')
    {
        $this->name = $name;
        $this->id = $id;
        $this->extra_classes = $this->stat('default_extra_classes');
        $this->extra_classes[] = 'svg-'.$this->name;
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
    public function extraClass($class)
    {
        $this->extra_classes[] = $class;
        return $this;
    }

    /**
     * @param $filePath
     * @return string
     */
    private function process($filePath)
    {
        $out = new DOMDocument();
        $out->load($filePath);

        $root = $out->documentElement;
        if ($this->fill) {
            $root->setAttribute('fill', $this->fill);
        }

        if ($this->width) {
            $root->setAttribute('width',  $this->width . 'px');
        }

        if ($this->height) {
            $root->setAttribute('height', $this->height . 'px');
        }

        if ($this->extra_classes) {
            $root->setAttribute('class', implode(' ', $this->extra_classes));
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
     * @return string
     */
    public function forTemplate()
    {
        $path = BASE_PATH . DIRECTORY_SEPARATOR .
            $this->stat('base_path') . DIRECTORY_SEPARATOR .
            $this->name .
            '.' . $this->stat('extension');

        return $this->process($path);
    }
}
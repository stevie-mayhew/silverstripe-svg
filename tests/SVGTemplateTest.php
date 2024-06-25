<?php

namespace StevieMayhew\SilverStripeSVG\Tests;

use SilverStripe\Dev\SapphireTest;
use StevieMayhew\SilverStripeSVG\SVGTemplate;

class SVGTemplateTest extends SapphireTest
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Set the base path to the test SVGs
        SVGTemplate::config()->set('base_path', 'tests');
    }


    public function testFullSvgPathForTemplate()
    {
        $svgTemplate = new SVGTemplate('logo.svg');
        $this->assertStringEndsWith('logo.svg', $svgTemplate->fullSvgPathForTemplate());

        $svgTemplate = $svgTemplate->customBasePath('custom');
        $this->assertStringEndsWith('custom/logo.svg', $svgTemplate->fullSvgPathForTemplate());
    }


    public function testIsRemoteSvg()
    {
        $svgTemplate = new SVGTemplate('logo.svg');
        $this->assertFalse($svgTemplate->isRemoteSvg());

        $svgTemplate = new SVGTemplate('https://example.com/logo.svg');
        $this->assertTrue($svgTemplate->isRemoteSvg());


        $svgTemplate = new SVGTemplate('//example.com/logo.svg');
        $this->assertTrue($svgTemplate->isRemoteSvg());
    }
}

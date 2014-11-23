<?php

/**
 * Class SVGTemplateProvider
 */
class SVGTemplateProvider implements TemplateGlobalProvider
{

    /**
     * @return array
     */
    public static function get_template_global_variables()
    {
        return array(
            'SVG'
        );
    }

    /**
     * @param $path
     * @return SVGTemplate
     */
    public static function SVG($path)
    {
        return new SVGTemplate($path);
    }

}
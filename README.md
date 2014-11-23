# SilverStripe SVG

Basic SVG support for SilverStripe templates

## Requirements
SilverStripe 3.1 or higher

## Installation
```composer require stevie-mayhew/silverstripe-svg:1.0.*```

## Configuration

You can set the base path for where your SVG's are stored. You can also add extra default classes to the SVG output

```yml
SVGTemplate:
  base_path: 'themes/base/production/svg/'
  default_extra_classes:
    - 'svg-image'
```

## Usage

```html
<!-- add svg -->
{$SVG('name')}
<!-- add svg with id 'testid' -->
{$SVG('with-id', 'testid')}
```
### Example Output
```html
<svg xmlns="http://www.w3.org/2000/svg" viewBox="248.5 0 464.8 560" enable-background="new 248.5 0 464.8 560" class="svg-name"><path d="M550.9 0H248.5v560h464.8V154.9L550.9 0zM648 149.3H534.1V41.1L648 149.3zm22.4 369.6H289.6V41.1h205.3v149.3h177.3v328.5h-1.8zM343.7 272.5h272.5v41.1H343.7zM343.7 369.6h272.5v41.1H343.7z"></path></svg>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="248.5 0 464.8 560" enable-background="new 248.5 0 464.8 560" class="svg-with-id svg-test-id" id="test-id"><path d="M550.9 0H248.5v560h464.8V154.9L550.9 0zM648 149.3H534.1V41.1L648 149.3zm22.4 369.6H289.6V41.1h205.3v149.3h177.3v328.5h-1.8zM343.7 272.5h272.5v41.1H343.7zM343.7 369.6h272.5v41.1H343.7z"></path></svg>

```

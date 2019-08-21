# EzProgressiveImageBundle

A bundle for EzPlatform to display images like [Medium](https://medium.com).

---

## Instalation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

    $ composer require gregleveque/ez-progressive-image-bundle

This command requires you to have Composer installed globally, as explained
in the *installation chapter* of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Gie\EzProgressiveImageBundle\EzProgressiveImageBundle(),
        );

        // ...
    }

    // ...
}
```
## Usage example

```twig
  {{ ez_render_field(content, 'image', {
    'parameters': {
      'alias': 'original', 'class': 'img-fluid w-100'}
}) }}
```

### Parameters

| Parameter | Type        | Description                                                          |
| --------- | ----------- | -------------------------------------------------------------------- |
| alias     | string      | Defaults to "original" (e.g. image originally uploaded)              |
| class     | string      | Allows setting CSS custom class name for the figure element          |
| width     | int, string | Allows forcing width of the image in the HTML                        |
| height    | int, string | Allows forcing height of the image in the HTML                       |
| exact     | bool        | Allows forcing height and width of the 'alias' to the figure element |
| caption   | string      | Allows adding a caption to the figure element                        |
| alt       | string      | Allows adding an alternative text on img elements                    |
| link      | object      | Allows setting a link on the figure element                          |
| * href    | string      |                                                                      |
| * title   | string      |                                                                      |
| * target  | string      |                                                                      |
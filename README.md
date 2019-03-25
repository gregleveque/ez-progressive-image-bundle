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

            new Gie\EzProgressiveImageBundle(),
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


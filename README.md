# Modularavel

> PLEASE, NEVER USE THIS PACKAGE.

In huge Laravel projects, we often need to deal with multiple route files, or hundreds of models, or hundreds of controllers, and the project becomes harder and harder to maintain. But how to avoid?

The concept of this package is to provide the ability to create small packages, or libraries, inside your Laravel project, without losing the comfort of the monorepo architecture.

<hr>

### Installation:
```shell
composer require erickjmenezes/modularavel
```

<hr>

### How to use:
```shell
php artisan make:lib <lib-name>
```
With this command, the package will generate a new library,
with minimal basic structure, inside `$PROJECT_ROOT$/libs/<lib-name>`.
Here you can develop and test your brand-new functionality, without affecting the existing ones.

**THAT'S IT!**

Take a look in the generated structure:
```
.
├── app/
├── public/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── tests/
└── libs/
    └── <lib-name>/
        ├── src/
        │   ├── Http/
        │   │   ├── Controllers/
        │   │   ├── Resources/
        │   │   └── Requests/
        │   ├── Models/
        │   └── Providers/
        ├── config/
        ├── resources/
        │   └── views/
        ├── tests/
        │   ├── Unit/
        │   └── Feature/
        ├── routes/
        ├── vendor/
        ├── composer.json
        └── composer.lock
```

### Customization

By default, the generated library will not have all the folders you see above.
You must choose what you want.
Run the following command to see what you can customize:
```shell
php artisan make:lib --help
```

### The generated library...
- Is automatically recognized. You don't need to move a finger. 
- Has its own `composer.json`.
  - Install packages inside the lib folder to develop with comfort. When everything is ready, just do a composer install in your project root folder and everything will be synced.
- Has everything ready to create new controllers, routes, views, commands, and whatever you want.
  - If you already know how to develop a package for laravel, you'll feel at home. It's a package like any other. If not, no problem, you'll feel at home too!
- Easy to decouple.
  - If you, for some reason, want to decouple the library from the libs folder and put in a standalone repo, it is almost as easy as copy-paste the contents to another repo.
  - You need a few extra steps to finish the move, but it is straightforward.
    - Run `composer remove libs/<lib-name>`.
    - Delete/Move the lib folder that is inside the `libs` folder.
    - Remove the reference from the root `composer.json`, in the `repositories` section.
    - Update the library `testbench.yaml` according to your needs. See `orchestral/testbench` for further information.
    

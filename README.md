modules
=======

modules is a PHP helper for mapping PHP classes to module files and autoloading
the modules as needed. It requires at least PHP 5.3.

Usage
=====

You'll need to include `modules.php` somewhere in your bootstrap:

    require 'lib/modules.php';

Then, use it to define your module files and what classes they contain:

    modules\from(function(__DIR__.'/bakery') {
        modules\add('cakes.php', 'Cake*');
        modules\add('pies.php', 'Pie*');
    });
    
    $cake = new Cake();         // would autoload __DIR__/bakery/cakes.php
    $pieCrust = new PieCrust(); // would autoload __DIR__/bakery/pies.php

If you have cake classes split over two separate files, you could do:

    modules\add('cakes/chocolate.php', 'ChocolateCake*');
    modules\add('cakes/vanilla.php', 'VanillaCake*');
    modules\add('pies.php', 'Pie*');

You can also specify multiple globs for one module:

    modules\add('cakes.php', array('Cake*', 'Candles', 'Lies'));

Manual loading
==============

By default, modules will add itself into the autoloading chain using the
`spl_autoload_register` method, and automatically include modules when you
reference the classes they match. You can turn this off by setting an env
variable before including the module:

    putenv('MODULES_AUTOLOAD=0');
    require 'lib/modules.php';
    
You can then manually load the modules as needed:

    modules\load('cakes/chocolate.php');

    // without the extension works too
    modules\load('cakes/chocolate');

But there's not really any reason to do this; you could just use require_once
instead. You could, however, use it to manually load classes:

    modules\loadClass('ChocolateCake');

That's a bit more useful for manual loading, as you're free to reorganize your
modules as needed and your imports won't be affected.

Definition order
================

Matching is done in order of definition, so make sure to be specific with your
globs and mapping order. For instance:

    modules\add('cakes.php', 'Cake*');
    modules\add('knives.php', array('CakeKnife', 'Knife*');
    
    // will incorrectly load cakes.php instead of knives.php
    $knife = new CakeKnife();

Instead, put the more specific definitions first:

    modules\add('knives.php', array('CakeKnife', 'Knife');
    modules\add('cakes.php', 'Cake*');

    // will correctly load knives.php
    $knife = new CakeKnife();

License
=======

(The MIT License)

Copyright ©2009 noonat

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the ‘Software’), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED ‘AS IS’, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

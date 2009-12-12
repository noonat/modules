<?php

require_once __DIR__.'/../mocks.php';

describe("Loader", function() {
    before_each(function($scope) {
        $cakes = new MockModule('cakes.php', __DIR__.'/../cakes.php', 'Cake*');
        $pies = new MockModule('pies.php', __DIR__.'/../pies.php', 'Pie*');
        $loader = new modules\Loader();
        $loader->addModule($cakes);
        $loader->addModule($pies);
        return array_merge($scope, compact('loader', 'cakes', 'pies'));
    });
    
    it("should start with an empty list of modules", function() {
        $loader = new modules\Loader();
        expect($loader->modules)->to_be_empty(); 
    });
    
    describe("addModule()", function() {
        it("should add a module to the list by name", function($scope) {
            extract($scope);
            $loader = new modules\Loader();
            $loader->addModule($cakes);
            expect($loader->modules)->to_have_count(1);
            expect($loader->modules['cakes.php'])->to_be($cakes);
            $loader->addModule($pies);
            expect($loader->modules)->to_have_count(2);
            expect($loader->modules['cakes.php'])->to_be($cakes);
            expect($loader->modules['pies.php'])->to_be($pies);
        });
    });
    
    describe("findModule()", function() {
        it("should return the module matching the passed name", function($scope) {
            extract($scope);
            expect($loader->findModule('cakes.php'))->to_be($cakes);
            expect($loader->findModule('pies.php'))->to_be($pies);
        });
       
        it("should accept module names without a .php extension", function($scope) {
            extract($scope);
            expect($loader->findModule('cakes'))->to_be($cakes);
            expect($loader->findModule('pies'))->to_be($pies);
        });
       
        it("should return null if there isn't module by that name", function($scope) {
            extract($scope);
            expect($loader->findModule('tarts'))->to_be_null();
        });
    });
    
    describe("findModuleMatching()", function() {
        it("should return the first module that matches the class name", function($scope) {
            extract($scope);
            expect($loader->findModuleMatching('Cake'))->to_be($cakes);
            expect($loader->findModuleMatching('Cakes'))->to_be($cakes);
            expect($loader->findModuleMatching('CakeIsALie'))->to_be($cakes);
            expect($loader->findModuleMatching('Pie'))->to_be($pies);
            expect($loader->findModuleMatching('Pies'))->to_be($pies);
            expect($loader->findModuleMatching('PieCrust'))->to_be($pies);
        });

        it("should return null if there isn't a matching module", function($scope) {
            extract($scope);
            expect($loader->findModuleMatching('Tart'))->to_be_null();
        });
    });
    
    describe("load()", function() {
        it("should call load() on the module", function($scope) {
            extract($scope);
            expect($cakes->loaded)->to_be_false();
            $loader->load('cakes.php');
            expect($cakes->loaded)->to_be_true();
            expect($pies->loaded)->to_be_false();
            $loader->load('pies.php');
            expect($pies->loaded)->to_be_true();
            expect($pies->loadCalled)->to_be(1);
            expect($cakes->loadCalled)->to_be(1);
        });
        
        it("should return the module", function($scope) {
            extract($scope);
            expect($loader->load('cakes.php'))->to_be($cakes);
            expect($loader->load('pies.php'))->to_be($pies);
        });
        
        it("should return null if the module doesn't exist", function($scope) {
            extract($scope);
            expect($loader->load('tarts.php'))->to_be_null();
        });
    });
    
    describe("loadClass()", function() {
        before_each(function($scope) {
            // recreating these here because we want real modules
            // instead of the mock modules
            $cakes = new modules\Module('cakes.php', __DIR__.'/../cakes.php', 'Cake*');
            $pies = new modules\Module('pies.php', __DIR__.'/../pies.php', 'Pie*');
            $loader = new modules\Loader();
            $loader->addModule($cakes);
            $loader->addModule($pies);
            return array_merge($scope, compact('loader', 'cakes', 'pies'));
        });
        
        it("should call load() on the matching module", function($scope) {
            extract($scope);
            $loader->loadClass('Cake');
            expect($cakes->loaded)->to_be_true();
        });
        
        it("should return the matched module", function($scope) {
            extract($scope);
            expect($loader->loadClass('Cake'))->to_be($cakes);
        });
        
        it("should return null if there wasn't a matching module", function($scope) {
            extract($scope);
            expect($loader->loadClass('Tart'))->to_be_null();
        });
        
        it("should fail if the class isn't defined after loading the module", function($scope) {
            extract($scope);
            expect(function() use($loader) {
                $loader->loadClass('Pie');
            })->to_throw('Exception', "class Pie matched module pies.php, but " .
                                      "class didn't exist after loading module");
        });
    });
});

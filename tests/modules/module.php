<?php

require_once __DIR__.'/../mocks.php';

describe("Module", function() {
    before_each(function($scope) {
        $cakes = new modules\Module('cakes.php', __DIR__.'/../cakes.php',
                                    array('Cake*', 'Candle*', 'Lie'));
        $pies = new modules\Module('pies.php', __DIR__.'/../pies.php', 'Pie*');
        return array_merge($scope, compact('cakes', 'pies'));
    });
    
    it("should set the passed parameters as attributes", function($scope) {
        extract($scope);
        expect($cakes->name)->to_be('cakes.php');
        expect($cakes->filename)->to_be(__DIR__.'/../cakes.php');
        expect($cakes->patterns)->to_be_type('array')->and_to_have_count(3);
        expect($cakes->patterns[0])->to_be('Cake*');
    });
    
    it("should convert the \$patterns argument into an array", function($scope) {
        extract($scope);
        expect($pies->patterns)->to_be_type('array')->and_to_have_count(1);
        expect($pies->patterns[0])->to_be('Pie*');
    });
    
    it("should not be loaded by default", function($scope) {
        extract($scope);
        expect($cakes->loaded)->to_be_false();
    });
    
    describe("load()", function() {
        it("should include the referenced file", function($scope) {
            extract($scope);
            expect(class_exists('Cake', false))->to_be_false();
            expect($cakes->load())->to_be_true();
            expect($cakes->loaded)->to_be_true();
            expect(class_exists('Cake', false))->to_be_true();
        });
    });
    
    describe("matches()", function() {
        it("should return true if the class name matches one of the patterns", function($scope) {
            extract($scope);
            expect($cakes->matches('Cake'))->to_be_true();
            expect($cakes->matches('Cakes'))->to_be_true();
            expect($cakes->matches('Candle'))->to_be_true();
            expect($cakes->matches('Candles'))->to_be_true();
            expect($cakes->matches('Lie'))->to_be_true();
            expect($pies->matches('Pie'))->to_be_true();
            expect($pies->matches('Pies'))->to_be_true();
        });
        
        it("should return false if the class name does not match a pattern", function($scope) {
            extract($scope);
            expect($cakes->matches('Caek'))->to_be_false();
            expect($cakes->matches('Lies'))->to_be_false();
        });
    });
});

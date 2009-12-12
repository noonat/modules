<?php

class MockModule extends modules\Module {
    public $loadCalled = 0;
    
    function load() {
        $this->loaded = true;
        $this->loadCalled += 1;
    }
}

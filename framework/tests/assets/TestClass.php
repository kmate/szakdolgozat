<?php

class TestClass extends TestParentClass implements TestInterface
{
    public function echoMethod()
    {
        return true;
    }
    
    public function zeroParameters() {}
    
    public function arrayParameter(array $a) {}
    
    public function alphabetParameters($a, $b, $c) {}
    
    public function optionalParameter($a, $b = 0) {}
    
    public function typedParameters(stdClass $a, stdClass $b) {}
    
    public function typedNullParameter(stdClass $a = null) {}
}

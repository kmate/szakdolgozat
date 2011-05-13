<?php

namespace fw\rpc;

interface Request
{
    function decode($rawData);
    
    function isValid();
}
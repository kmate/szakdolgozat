<?php

namespace fw\rpc;

use \fw\config\Configuration;

interface Service
{
    function getConfiguration();
    function setConfiguration(Configuration $config);
}
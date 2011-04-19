<?php

namespace fw\config;

class Exception extends \fw\Exception
{
    const INVALID_SECTION_NAME   = 1;
    const UNABLE_TO_READ_FILE    = 2;
    const UNABLE_TO_PARSE_FILE   = 3;
    const MISSING_PARENT_SECTION = 4;
}

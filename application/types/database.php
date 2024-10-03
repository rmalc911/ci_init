<?php

namespace db;

defined('BASEPATH') or exit('No direct script access allowed');

interface string_types extends \Stringable {
}

interface datetime extends string_types {
}

interface date extends string_types {
}

interface time extends string_types {
}

interface varchar extends string_types {
}

interface text extends string_types {
}

interface enum extends string_types {
}

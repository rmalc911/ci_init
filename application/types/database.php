<?php
defined('BASEPATH') or exit('No direct script access allowed');

abstract class string_types implements Stringable {
}

abstract class num_types implements Stringable {
}

abstract class datetime extends string_types {
}

abstract class date extends string_types {
}

abstract class varchar extends string_types {
}

abstract class text extends string_types {
}

abstract class enum extends string_types {
}

abstract class decimal extends num_types {
}

abstract class table {
}

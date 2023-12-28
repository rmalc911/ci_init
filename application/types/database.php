<?php

namespace db;

defined('BASEPATH') or exit('No direct script access allowed');

interface Stringable {
	#region Functions

	/**
	 * Gets a string representation of the object
	 * @return string Returns the `string` representation of the object.
	 */
	function __toString();

	#endregion
}

interface string_types extends Stringable {
}

interface datetime extends string_types {
}

interface date extends string_types {
}

interface varchar extends string_types {
}

interface text extends string_types {
}

interface enum extends string_types {
}

interface table extends Stringable {
}

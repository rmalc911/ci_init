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

abstract class string_types implements Stringable {
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

abstract class table implements Stringable {
	function __toString() {
		return __CLASS__;
	}
}

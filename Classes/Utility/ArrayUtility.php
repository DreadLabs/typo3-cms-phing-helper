<?php
class ArrayUtility {

	/**
	 * Renumber the keys of an array to avoid leaps is keys are all numeric.
	 *
	 * Is called recursively for nested arrays.
	 *
	 * Example:
	 *
	 * Given
	 *  array(0 => 'Zero' 1 => 'One', 2 => 'Two', 4 => 'Three')
	 * as input, it will return
	 *  array(0 => 'Zero' 1 => 'One', 2 => 'Two', 3 => 'Three')
	 *
	 * Will treat keys string representations of number (ie. '1') equal to the
	 * numeric value (ie. 1).
	 *
	 * Example:
	 * Given
	 *  array('0' => 'Zero', '1' => 'One' )
	 * it will return
	 *  array(0 => 'Zero', 1 => 'One')
	 *
	 * @author Susanne Moog <typo3@susanne-moog.de>
	 * @see typo3/sysext/core/Classes/Utility/ArrayUtility.php
	 *
	 * @param array $array Input array
	 * @param integer $level Internal level used for recursion, do *not* set from outside!
	 * @return array
	 */
	public static function renumberKeysToAvoidLeapsIfKeysAreAllNumeric(array $array = array(), $level = 0) {
		$level++;
		$allKeysAreNumeric = TRUE;
		foreach (array_keys($array) as $key) {
			if (is_numeric($key) === FALSE) {
				$allKeysAreNumeric = FALSE;
				break;
			}
		}
		$renumberedArray = $array;
		if ($allKeysAreNumeric === TRUE) {
			$renumberedArray = array_values($array);
		}
		foreach ($renumberedArray as $key => $value) {
			if (is_array($value)) {
				$renumberedArray[$key] = self::renumberKeysToAvoidLeapsIfKeysAreAllNumeric($value, $level);
			}
		}

		return $renumberedArray;
	}

	/**
	 * Exports an array as string.
	 * Similar to var_export(), but representation follows the TYPO3 core CGL.
	 *
	 * See unit tests for detailed examples
	 *
	 * @author Susanne Moog <typo3@susanne-moog.de>
	 * @see typo3/sysext/core/Classes/Utility/ArrayUtility.php
	 *
	 * @param array $array Array to export
	 * @param integer $level Internal level used for recursion, do *not* set from outside!
	 * @return string String representation of array
	 * @throws \RuntimeException
	 */
	public static function arrayExport(array $array = array(), $level = 0) {
		$lines = 'array(' . chr(10);
		$level++;
		$writeKeyIndex = FALSE;
		$expectedKeyIndex = 0;
		foreach ($array as $key => $value) {
			if ($key === $expectedKeyIndex) {
				$expectedKeyIndex++;
			} else {
				// Found a non integer or non consecutive key, so we can break here
				$writeKeyIndex = TRUE;
				break;
			}
		}
		foreach ($array as $key => $value) {
			// Indention
			$lines .= str_repeat(chr(9), $level);
			if ($writeKeyIndex) {
				// Numeric / string keys
				$lines .= is_int($key) ? $key . ' => ' : '\'' . $key . '\' => ';
			}
			if (is_array($value)) {
				if (count($value) > 0) {
					$lines .= self::arrayExport($value, $level);
				} else {
					$lines .= 'array(),' . chr(10);
				}
			} elseif (is_int($value) || is_float($value)) {
				$lines .= $value . ',' . chr(10);
			} elseif (is_null($value)) {
				$lines .= 'NULL' . ',' . chr(10);
			} elseif (is_bool($value)) {
				$lines .= $value ? 'TRUE' : 'FALSE';
				$lines .= ',' . chr(10);
			} elseif (is_string($value)) {
				// Quote \ to \\
				$stringContent = str_replace('\\', '\\\\', $value);
				// Quote ' to \'
				$stringContent = str_replace('\'', '\\\'', $stringContent);
				$lines .= '\'' . $stringContent . '\'' . ',' . chr(10);
			} else {
				throw new RuntimeException('Objects are not supported', 1342294986);
			}
		}
		$lines .= str_repeat(chr(9), ($level - 1)) . ')' . ($level - 1 == 0 ? '' : ',' . chr(10));

		return $lines;
	}
}
?>
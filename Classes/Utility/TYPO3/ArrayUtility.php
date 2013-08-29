<?php

/***************************************************************
 * Copyright notice
 *
 * (c) 1999-2011 Kasper Skårhøj (kasperYYYY@typo3.com)
 * (c) 2011 Susanne Moog <typo3@susanne-moog.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Array Utilities
 *
 */
class ArrayUtility {

    /**
     * Merges two arrays recursively and "binary safe" (integer keys are
     * overridden as well), overruling similar values in the first array
     * ($arr0) with the values of the second array ($arr1)
     * In case of identical keys, ie. keeping the values of the second.
     *
     * @author Kasper Skårhøj <kasperYYYY@typo3.com>
     * @see typo3/sysext/core/Classes/Utility/GeneralUtility.php::array_merge_recursive_overrule
     *
     * @param array $arr0 First array
     * @param array $arr1 Second array, overruling the first array
     * @param boolean $notAddKeys If set, keys that are NOT found in $arr0 (first array) will not be set. Thus only existing value can/will be overruled from second array.
     * @param boolean $includeEmptyValues If set, values from $arr1 will overrule if they are empty or zero. Default: TRUE
     * @param boolean $enableUnsetFeature If set, special values "__UNSET" can be used in the second array in order to unset array keys in the resulting array.
     * @return array Resulting array where $arr1 values has overruled $arr0 values
     */
    static public function array_merge_recursive_overrule(array $arr0, array $arr1, $notAddKeys = FALSE, $includeEmptyValues = TRUE, $enableUnsetFeature = TRUE)
    {
        foreach ($arr1 as $key => $val) {
            if ($enableUnsetFeature && $val === '__UNSET') {
                unset($arr0[$key]);
                continue;
            }
            if (isset($arr0[$key]) && is_array($arr0[$key])) {
                if (is_array($arr1[$key])) {
                    $arr0[$key] = self::array_merge_recursive_overrule($arr0[$key], $arr1[$key], $notAddKeys, $includeEmptyValues, $enableUnsetFeature);
                }
            } elseif (
                                (!$notAddKeys || isset($arr0[$key])) &&
                                ($includeEmptyValues || $val)
            ) {
                $arr0[$key] = $val;
            }
        }
        reset($arr0);
        return $arr0;
    }

    /**
     * Sorts an array recursively by key
     *
     * @param $array Array to sort recursively by key
     * @return array Sorted array
     */
    static public function sortByKeyRecursive(array $array)
    {
        ksort($array);
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $array[$key] = self::sortByKeyRecursive($value);
            }
        }

        return $array;
    }

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
    static public function renumberKeysToAvoidLeapsIfKeysAreAllNumeric(array $array = array(), $level = 0)
    {
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
    static public function arrayExport(array $array = array(), $level = 0)
    {
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
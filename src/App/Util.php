<?php
/**
 * User: Yerlen Zhubangaliyev (yz@yz.kz)
 * Date: 28.11.13
 * Time: 15:27
 */

namespace App
{
    /**
     * Class Util
     * @package App
     */
    trait Util
    {
        /**
         * Перевод массива в строку пути
         *
         * @param $inputArray
         * @param bool $absolutePath
         * @param bool $isFile
         * @param bool $delimiter
         * @return string
         */
        public static function arrayToPath($inputArray, $absolutePath = true, $isFile = true, $delimiter = false)
        {
            $delimiter = (!$delimiter) ? \App::$directorySeparator : $delimiter;
            $result    = ($absolutePath) ? $delimiter : "";

            if (\is_array($inputArray) && \count($inputArray) > 0) {

                foreach ($inputArray as $inputArrayValue) {
                    $inputArrayValue = \trim($inputArrayValue, $delimiter);
                    $result .= $inputArrayValue . $delimiter;
                }

                if ($isFile) {
                    $result = \rtrim($result, $delimiter);
                }
            }

            return $result;
        }

        /**
         * Перевод массива в строку PHP NAMESPACE
         *
         * @param $inputArray
         * @param bool $absolutePath
         * @return string
         */
        public static function arrayToNamespace($inputArray, $absolutePath = true)
        {
            return self::arrayToPath($inputArray, $absolutePath, true, \App::$namespaceSeparator);
        }

        /**
         * Перевод строки пути в массив
         *
         * @param $inputString
         * @return array
         */
        public static function pathToArray($inputString)
        {
            $result = [];

            if (\is_string($inputString) && \strlen($inputString) > 0) {
                $result = \array_filter(\explode(\App::$directorySeparator, $inputString));
            }

            return $result;
        }

        /**
         * @param $inputString
         * @return string
         */
        public static function parentDirectory($inputString)
        {
            $result = self::pathToArray($inputString);
            \array_pop($result);

            return self::arrayToPath($result);
        }

        /**
         * Конвертирует значения в UPPERCASE в одномерном массиве
         *
         * @param $inputArray
         * @return array
         */
        public static function arrayValuesToUpper($inputArray)
        {
            return \array_map(function ($value) {
                return \strtoupper($value);
            }, $inputArray);
        }

        /**
         * Конвертирует значения в LOWERCASE в одномерном массиве
         *
         * @param $inputArray
         * @return array
         */
        public static function arrayValuesToLower($inputArray)
        {
            return \array_map(function ($value) {
                return \strtolower($value);
            }, $inputArray);
        }

        /**
         * Конвертирует ключи в UPPERCASE в одномерном массиве
         *
         * @param $inputArray
         * @return array
         */
        public static function arrayKeysToUpper($inputArray)
        {
            return \array_change_key_case($inputArray, \CASE_UPPER);
        }

        /**
         * Конвертирует ключи в LOWERCASE в одномерном массиве
         *
         * @param $inputArray
         * @return array
         */
        public static function arrayKeysToLower($inputArray)
        {
            return \array_change_key_case($inputArray, \CASE_LOWER);
        }
    }
}

 
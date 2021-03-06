<?php

/**
 * Config.php - Jaxon config reader
 *
 * Read the Jaxon config from a data array and save it locally.
 *
 * @package jaxon-core
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2016 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-2-Clause BSD 2-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Config;

use Jaxon\Jaxon;

class Config
{
    private static $aOptions;

    /**
     * Recursively read Jaxon options from a data array
     *
     * @param array         $aOptions           The options array
     * @param string        $sPrefix            The prefix for option names
     * @param integer       $nDepth             The depth from the first call
     *
     * @return void
     */
    private static function readOptions(array $aOptions, $sPrefix = '', $nDepth = 0)
    {
        $sPrefix = (string)$sPrefix;
        $nDepth = intval($nDepth);
        // Check the max depth
        if($nDepth < 0 || $nDepth > 5)
        {
            throw new \Jaxon\Exception\Config\Data('depth', $sPrefix, $nDepth);
        }
        if($nDepth == 0)
        {
            self::$aOptions = array();
        }
        foreach ($aOptions as $sName => $xOption)
        {
            if(is_array($xOption))
            {
                // Recursively read the options in the array
                self::readOptions($xOption, $sPrefix . $sName . '.', $nDepth + 1);
            }
            else if(is_string($xOption) || is_numeric($xOption) || is_bool($xOption))
            {
                // Save the value of this option
                self::$aOptions[$sPrefix . $sName] = $xOption;
            }
        }
    }

    /**
     * Read and set Jaxon options from a data array
     *
     * @param array         $aOptions           The options array
     * @param string        $sKeys              The keys of the options in the array
     *
     * @return void
     */
    public static function setOptions(array $aOptions, $sKeys = '')
    {
        // Find the config array in the input data
        $aKeys = explode('.', (string)$sKeys);
        foreach ($aKeys as $sKey)
        {
            if(($sKey))
            {
                if(!array_key_exists($sKey, $aOptions) || !is_array($aOptions[$sKey]))
                {
                    throw new \Jaxon\Exception\Config\Data('missing', $sKeys);
                }
                $aOptions = $aOptions[$sKey];
            }
        }
        // Read options from the data
        self::readOptions($aOptions);
        // Set the options in the core library
        $jaxon = jaxon();
        $jaxon->setOptions(self::$aOptions);
    }
}

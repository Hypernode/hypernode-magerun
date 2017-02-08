<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2017 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */

namespace Hypernode\PasswordCracker;

use Hypernode\PasswordCracker\Mutator;

class RuleFactory
{
    /**
     * @return array
     */
    protected function getValidRules()
    {
        $mutators = array(
            Mutator\AppendCharacter::class,
            Mutator\Capitalize::class,
            Mutator\DeleteAtN::class,
            Mutator\Duplicate::class,
            Mutator\DuplicateAll::class,
            Mutator\DuplicateFirstN::class,
            Mutator\DuplicateLastN::class,
            Mutator\DuplicateN::class,
            Mutator\ExtractRange::class,
            Mutator\InsertAtN::class,
            Mutator\InvertCapitalize::class,
            Mutator\Lowercase::class,
            Mutator\Nothing::class,
            Mutator\OmitRange::class,
            Mutator\OverwriteAtN::class,
            Mutator\PrependCharacter::class,
            Mutator\Purge::class,
            Mutator\Reflect::class,
            Mutator\Replace::class,
            Mutator\Reverse::class,
            Mutator\RotateLeft::class,
            Mutator\RotateRight::class,
            Mutator\ToggleAt::class,
            Mutator\ToggleCase::class,
            Mutator\TruncateRight::class,
            Mutator\TruncateAtN::class,
            Mutator\TruncateLeft::class,
            Mutator\Uppercase::class,
            Mutator\SwapFront::class,
            Mutator\SwapBack::class,
            Mutator\SwapAtN::class,
            Mutator\BitwiseShiftLeft::class,
            Mutator\BitwiseShiftRight::class,
            Mutator\AsciiIncrement::class,
            Mutator\AsciiDecrement::class,
            Mutator\ReplaceNPlusOne::class,
            Mutator\ReplaceNMinusOne::class,
            Mutator\DuplicateBlockFront::class,
            Mutator\DuplicateBlockBack::class,
            Mutator\Title::class,
            Mutator\Memorize::class,
            Mutator\PrependMemory::class,
            Mutator\AppendMemory::class,
            Mutator\ExtractMemory::class,
        );

        $m = array();
        foreach ($mutators as $mutator) {
            $id = $mutator::getIdentifier();
            $m[$id] = array(
                'identifier' => $id,
                'length'     => $mutator::getLength(),
                'class'      => $mutator,
            );
        }

        return $m;
    }

    public static function createFromDefinition($definition)
    {

        $validMutators = self::getValidRules();

        $i = 0;
        $mutators = array();
        try {
            while ($i < strlen($definition)) {
                $identifier = $definition[$i];
                if ($identifier === ' ') {
                    $i++;
                    continue;
                }
                if (!isset($validMutators[$identifier])) {
                    throw new \InvalidArgumentException(
                        sprintf('Mutator "%s" not supported in "%s"', $identifier, $definition)
                    );
                }
                $mutator = $validMutators[$identifier];
                $current = '';
                for ($x = 0; $x < $mutator['length']; $x++) {
                    $current .= $definition[$i + $x];
                }

                // validate rule
//                if (!$m[$c]['c']::validate($current)) {
//                    throw new \InvalidArgumentException(sprintf('Invalid mutator "%s" in "%s"', $current, $definition));
//                }

                $mutators[] = new $validMutators[$identifier]['class']($current);

                $i = $i + $mutator['length'];
            }

            return new Rule($mutators);
        } catch (\Exception $e) {
            $failedRules[] = $definition;
        }

        return false;
    }

    /**
     * @param $definitions
     * @return \ArrayIterator
     */
    public static function createFromDefinitionSet($definitions)
    {
        $rules = array();
        foreach ($definitions as $definition) {
            $rule = self::createFromDefinition($definition);
            if ($rule) {
                $rules[] = $rule;
            }
        }

        return new \ArrayIterator($rules);
    }
}

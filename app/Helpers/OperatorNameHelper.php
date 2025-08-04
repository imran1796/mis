<?php

namespace App\Helpers;

class OperatorNameHelper
{
    public static function expandOperators(array $inputOperators): array
    {
        $aliases = [
            'SKN' => ['SKN', 'SNK'],
            'SNK' => ['SKN', 'SNK'],
        ];
        
        $result = [];

        foreach ($inputOperators as $operator) {
            if (isset($aliases[$operator])) {
                $result = array_merge($result, $aliases[$operator]);
            } else {
                $result[] = $operator;
            }
        }

        return array_unique($result);
    }
}

<?php

namespace App\Enums;

use InvalidArgumentException;

enum RegionCode: string
{
    case EU  = 'Europe';
    case FE  = 'Far East';
    case AE  = 'Arabian Gulf';
    case MED = 'Mediterranean';
    case AW  = 'Africa West';
    case ISC = 'Indian Subcontinent';
    case CA  = 'Central America';
    case MID = 'Middle East';
    case NZ  = 'New Zealand';
    case AS  = 'Southeast Asia';
    case AF  = 'Africa';
    case CIS = 'CIS Countries';
    case PNG = 'Papua New Guinea';

    /**
     * Returns the label for the region code
     */
    public function label(): string
    {
        return $this->value;
    }

    /**
     * Returns an array of all region codes with their labels
     */
    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->name => $case->label()])
            ->toArray();
    }

    /**
     * Returns the enum value for a given code (name), or throws an exception if not found
     * 
     * @throws InvalidArgumentException
     */
    public static function fromCode(string $code): self
    {
        $code = strtoupper(trim($code)); // Normalize input
        
        // Loop through all enum cases and check for a match based on the name
        foreach (self::cases() as $case) {
            if ($case->name === $code) {
                return $case;
            }
        }
        
        // return $case;
    }
    
}

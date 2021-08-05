<?php

namespace App\Helpers;

class UserAmountMaxHeap extends \SplMaxHeap
{
    public function compare($value1, $value2)
    {
        return $value1['amount'] - $value2['amount'];
    }
}

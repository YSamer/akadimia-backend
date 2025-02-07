<?php

namespace Tests\Feature\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WirdRangeTest extends TestCase
{
    public function allMyMahfoozPages($start, $end, $min = 1, $max = 604)
    {
        $completedRange = [];
        $current = $start;

        while ($current != $end) {
            $completedRange[] = $current;
            $current++;
            if ($current > $max) {
                $current = $min;
            }
        }
        $completedRange[] = $end;
        return $completedRange;
    }

    public function myMahfoozPagesNum($start, $end, $min = 1, $max = 604)
    {
        $myMahfoozPagesNum = 0;
        $current = $start;

        while ($current != $end) {
            $myMahfoozPagesNum++;
            $current++;
            if ($current > $max) {
                $current = $min;
            }
        }
        $myMahfoozPagesNum++;
        return $myMahfoozPagesNum;
    }

    public function splitMyRange($start, $end, $range, $min = 1, $max = 604)
    {
        $completedRange = $this->allMyMahfoozPages($start, $end, $min, $max);
        $totalCount = count($completedRange);
        $fullPartsCount = intdiv($totalCount, $range);

        $splitRanges = [];
        $currentIndex = 0;

        for ($i = 0; $i < $fullPartsCount; $i++) {
            $splitRanges[] = array_slice($completedRange, $currentIndex, $range);
            $currentIndex += $range;
        }

        return $splitRanges;
    }


    public function getArrayContainingValue($start, $end, $range, $value, $min = 1, $max = 604)
    {
        $splitRanges = $this->splitMyRange($start, $end, $range, $min, $max);

        foreach ($splitRanges as $srange) {
            if (in_array($value, $srange)) {
                return $srange;
            }
        }

        return [];
    }

    /**
     * @outputBuffering enabled
     */
    public function testSplitMyRange()
    {
        // Test splitting the range between x = 3 and y = 13 into 3 parts
        $result = $this->splitMyRange(5, 2, 2, 1, 20);
        $this->assertEquals(
            [
                [5, 6],
                [7, 8],
                [9, 10],
                [11, 12],
                [13, 14],
                [15, 16],
                [17, 18],
                [19, 20],
                [1, 2],
            ],
            $result
        );

        $result = $this->getArrayContainingValue(11, 5, 3, 4, 1, 20);

        $this->assertEquals([3, 4, 5], $result);

    }

}

<?php
function allMyMahfoozPages($start, $end, $min = 1, $max = 604)
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

function myMahfoozPagesNum($start, $end, $min = 1, $max = 604)
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

function splitMyRange($start, $end, $range, $min = 1, $max = 604)
{
    $completedRange = allMyMahfoozPages($start, $end, $min, $max);
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


function getArrayContainingValue($start, $end, $range, $value, $min = 1, $max = 604)
{
    if ($range == null || $range == 0)
        return [null];

    $splitRanges = splitMyRange($start, $end, $range, $min, $max);

    foreach ($splitRanges as $srange) {
        if (in_array($value, $srange)) {
            return $srange;
        }
    }

    return [null];
}

function getNextArrayFromContainingValue($start, $end, $range, $value, $min = 1, $max = 604)
{
    if ($range == null || $range == 0)
        return null;

    $splitRanges = splitMyRange($start, $end, $range, $min, $max);

    if ($value == null) {
        return $splitRanges[0][0];
    }
    foreach ($splitRanges as $index => $srange) {
        if (in_array($value, $srange)) {
            return ($splitRanges[$index + 1] ?? $splitRanges[0])[0];
        }
    }

    return null;
}
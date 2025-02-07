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

function getAhzaab()
{
    $ahzaab = [];
    $startPage = 1;
    for ($hizb = 1; $hizb <= 60; $hizb++) {
        $endPage = ($hizb == 1) ? $startPage + 10 : $startPage + 9;
        if ($hizb == 60)
            $endPage = 604;
        $ahzaab[] = [
            "hizb" => $hizb,
            "start_page" => $startPage,
            "end_page" => $endPage
        ];
        $startPage = $endPage + 1;
    }
    return $ahzaab;
}

function getAjzaa()
{
    $ajzaa = [];
    $startPages = [
        1,
        22,
        42,
        62,
        82,
        102,
        121,
        141,
        161,
        181,
        201,
        221,
        241,
        262,
        282,
        302,
        322,
        342,
        362,
        382,
        402,
        422,
        442,
        462,
        482,
        502,
        522,
        542,
        562,
        582
    ];

    for ($juz = 1; $juz <= 30; $juz++) {
        $startPage = $startPages[$juz - 1];
        $endPage = ($juz == 30) ? 604 : $startPages[$juz] - 1;

        $ajzaa[] = [
            "juz" => $juz,
            "start_page" => $startPage,
            "end_page" => $endPage
        ];
    }
    return $ajzaa;
}

function getAhzaabByRange($from, $to)
{
    $ahzaab = getAhzaab();
    $result = [];

    foreach ($ahzaab as $hizb) {
        if ($hizb["start_page"] <= $to && $hizb["end_page"] >= $from) {
            $result[] = $hizb;
        }
    }

    return $result;
}

function getHizbByPage($page)
{
    $ahzaab = getAhzaab();
    foreach ($ahzaab as $hizb) {
        if ($page >= $hizb["start_page"] && $page <= $hizb["end_page"]) {
            return $hizb;
        }
    }
    return null;
}

function getJuzByPage($page)
{
    $ajzaa = getAjzaa();
    foreach ($ajzaa as $juz) {
        if ($page >= $juz["start_page"] && $page <= $juz["end_page"]) {
            return $juz;
        }
    }
    return null;
}


function getHizbByPageInRange($page, $from, $to)
{
    $ahzaab = getAhzaabByRange($from, $to);
    foreach ($ahzaab as $hizb) {
        if ($page >= $hizb["start_page"] && $page <= $hizb["end_page"]) {
            return $hizb;
        }
    }
    return $ahzaab[0];
}
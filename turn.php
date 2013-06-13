<?php

$a = [3,41,32,44,51,61,1,53,23,546,12];

$a = qSort($a);
print_r($a);

function qSort($a) {
	quickSort($a, 0, count($a) - 1);
	return $a;
}

function quickSort(& $a, $low, $high)
{
	if ($low < $high) {
		$pivot = sortArray($a, $low, $high);
		quickSort($a, 0, $pivot - 1);
		quickSort($a, $pivot+1, $high);
	}
	return $a;
}

function sortArray(& $a, $low, $high) {
	$v = $a[$low];
	$i = 0;
	while ($low < $high) {
		while ($low < $high && $a[$high] >= $v) {
			$high --;
		}
		swap($a, $low, $high);
		while ($low < $high && $a[$low] <= $v) {
			$low ++;
		}
		$i ++;
		if ($i > 10 ){
			break;
		}
		swap($a, $low, $high);

	}
	return $low;
}

function swap(& $a, $key1, $key2) {
	$tmp = $a[$key1];
	$a[$key1] = $a[$key2];
	$a[$key2] = $tmp;
}
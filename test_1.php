<?php
function quicksort($array = [])
{
	if (count($array) <= 1) {
		return $array;
	}
	$less = $greater = [];
	$k = array_rand($array);
	$pivot = $array[$k];
	unset($array[$k]);
	foreach ($array as $key => $value) {
		if ($value < $pivot) {
			$less[] = $value;
		} else {
			$greater[] = $value;
		}
	}
	return array_merge(quicksort($less), [$pivot], quicksort($greater));
}

print_r(quicksort([1,4,52,642,12,5,34,61,2,5,6,7,3,41,3]));
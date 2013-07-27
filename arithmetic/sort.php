<?php
$a = [3,41,32,44,51,61,1,53,23,546,12];

function insertSort($a) 
{
	$count = count($a);
	for ($i = 1; $i < $count; $i ++) {
		$tmp = $a[$i];
		$j = $i - 1;
		while (isset($a[$j]) && $a[$j] > $tmp) {
			$a[$j + 1] = $a[$j];
			$a[$j] = $tmp;
			$j --;
		}
	}
	return $a;
}

function selectSort($a)
{
	$count = count($a);
	for ($i = 0; $i < $count; $i ++) {
		$k = $i;
		for ($j = $i + 1; $j < $count; $j++) {
			if ($a[$k] > $a[$j]) {
				$k = $j;
			}
			if ($k != $i) {
				$tmp = $a[$i];
				$a[$i] = $a[$k];
				$a[$k] = $tmp;
			}

		}
	}
	return $a;

}


var_dump(selectSort($a));
//var_dump(insertSort($a));



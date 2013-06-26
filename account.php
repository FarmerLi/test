<?php
$ln = mysql_connect('192.168.123.243', 'root', 'mydb123');
mysql_select_db('sap_prod');
mysql_query('set names utf8');


//$query = mysql_query("select * from account_single where single_type = 6 and supplier_id = 156");

$query = mysql_query("select * from stockin_order where supplier_id = 156");
while ($row = mysql_fetch_array($query)) {
    // $art_ids = get_art_ids($row);
    // foreach ($art_ids as $id) {
    //     if (match_art($id) == false) {
    //         echo "artID: " . $id . "\n";
    //     }
    // }
    if (match_single_amount($row) == false) {
        echo "singleID : " . $row['id'] . "\n";
    }
}


function get_art_ids($single)
{
    $query = mysql_query("select * from articulation_detail where single_type in (1,2) and single_id = {$single['id']}");
    $art_ids = [];
    while ($row = mysql_fetch_array($query)) {
        $art_ids[] = $row['art_id'];
    }
    return array_unique($art_ids);

}

function match_art($id)
{
    $query = mysql_query("select * from articulation_detail where art_id = $id");
    $target = $source = 0;
    while ($row = mysql_fetch_array($query)) {
        if ($row['detail_type'] == 'source') {
            $source += $row['act_amount'];
        } else {
            $target += $row['act_amount'];
        }
    }
    if (bccomp($target, $source,2 ) != 0) {
        return false;
    }
    return true;
}

function match_single_amount($single)
{
    $query = mysql_query("select sum(a.act_amount) from articulation_detail as a 
        left join account_articulation as b on a.art_id = b.id 
        where a.single_type in (1,2) and a.single_id = {$single['id']} and b.status != -1");
    $field = mysql_fetch_row($query);
    if (bccomp($single['pay_amount'], $field[0], 2) != 0) {
        return false;
    }
    return true;
}
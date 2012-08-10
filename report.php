<?php

define(TOKEN, file_get_contents('TOKEN'));

require 'freeAgent_cached.class.php';
require 'freeAgent_timeslips.class.php';

$monday = date('Y-m-d', strtotime('this week monday'));
$friday = date('Y-m-d', strtotime('this week friday'));

$fa = new freeAgent_cached(TOKEN);
$t  = new freeAgent_timeslips($fa->getTimeslips($monday, $friday));

echo date('d/m/y', strtotime($monday)).' - '.date('d/m/y', strtotime($friday))."\n";
$total = 0;
foreach($t->reportByProject() as $project) {
    $p = $fa->getProject( $project['id'] );
    echo sprintf("% 19s % 5.2f\n", $p->name, $project['hours']);
    $total += $project['hours'];
}
echo str_repeat('=', 19)." =====\n";
echo sprintf("% 19s % 5.2f\n", 'TOTAL', $total);


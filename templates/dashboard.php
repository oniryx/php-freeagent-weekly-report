<html>
  <head>
    <title>Freeagent timeslips analytics</title>
  </head>
  <body>
  <form method="get">
    <input type="text" name="date_from" value="<?php echo $date_from ?>" />
    <button>Submit</button>
  </form>
  <table>
    <thead>
      <tr>
        <th>Client</th>
        <th>Project</th>
        <th>B</th>
        <th>T</th>
        <th>V</th>
      </tr>
    </thead>
    <tbody><?php foreach($hours as $p_id=>$h) { 
    $project = $projects[ $p_id ];
    $contact = $contacts[ basename($project['contact']) ];
    ?><tr><td><?php echo $contact['first_name'].' '.$contact['last_name'].' </td><td> '.$project['name'].'</td><td>'.round($h['b'],2).'</td><td>'.round($h['t'],2).'</td><td>'.number_format($h['v'],0).'</td></tr>';
  }
  ?></tbody>
  <tfoot>
    <tr>
      <th colspan="2">Total</th>
      <th><?php echo round($total['b'],2) ?></th>
      <th><?php echo round($total['t'],2) ?></th>
      <th><?php echo number_format($total['v'],0) ?></th>
    </tr>
  </tfoot>
  </table>
  </body>
</html>

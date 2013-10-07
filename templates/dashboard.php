<html>
  <head>
    <title>Freeagent timeslips analytics</title>
  </head>
  <body>
  <form method="get">
    <input type="text" name="date_from" value="<?php echo $date_from ?>" />
    <input type="text" name="date_to" value="<?php echo $date_to ?>" />
    <button>Submit</button>
  </form>
  <table>
    <thead>
      <tr>
        <th>Client</th>
        <th>Project</th>
        <th>B</th>
        <th>T</th>
      </tr>
    </thead>
    <tbody><?php foreach($hours as $p_id=>$h) { 
    $project = $projects[ $p_id ];
    $contact = $contacts[ basename($project['contact']) ];
    ?><tr><td><?php echo $contact['first_name'].' '.$contact['last_name'].' </td><td> '.$project['name'].'</td><td>'.$h['b'].'</td><td>'.$h['t'].'</td></tr>';
  }
  ?></tbody>
  <tfoot>
    <tr>
      <th colspan="2">Total</th>
      <th><?php echo $total['b'] ?></th>
      <th><?php echo $total['t'] ?></th>
    </tr>
  </tfoot>
  </table>
  </body>
</html>

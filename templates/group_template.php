<html>

<head>
<style type="text/css">
  table, td, th { border: 1px solid; }
  td { width: 60px; }
  td.void { background-color: rgb(153, 153, 153);  }
  td.small { font-size:small; }
  td.center { text-align:center }
</style>
</head>

<body>
<table style="border:2px solid;">
  <tr>
    <th>#</th>
    <th>Name</th>
    <th class="small">Country</th>
    <th class="small">Total<br/>Points</th>
    <th class="small">Rank</th>
    <?php for($i=0;$i<count($players);$i++): ?>
      <th><?php echo ($i+1); ?></th>
    <?php endfor; ?>
    <th>Result</th>
  <tr>
  <?php for($i=0;$i<count($players);$i++): ?>
    <tr>
      <td class="center"><?php echo ($i+1); ?></td>
      <td><?php echo htmlspecialchars($players[$i]->name); ?></td>
      <td class="center"><?php echo $players[$i]->country_flag_s_tag(); ?></td>
      <td class="center"><?php echo $players[$i]->calculate_point(); ?></td>
      <td class="center"><?php echo Util::ordinalize($player_ranks[$players[$i]->name]+1); ?></td>
      <?php for($j=0;$j<count($players);$j++): ?>
        <?php if($i == $j): ?>
          <td class="void">&nbsp;</td>
        <?php else: ?>
          <?php if($result = $players[$i]->get_result($players[$j])): ?>
            <td class="center">
              <a target="_blank" href="http://81dojo.com/kifuviewer.html?kid=<?php echo $result->kifu_id; ?>"><?php echo $result->result_label_for($players[$i]); ?></a>
            </td>
          <?php else: ?>
            <td>&nbsp;</td>
          <? endif ?>
        <?php endif; ?>
      <?php endfor; ?>
      <td class="center"><?php echo isset($players[$i]->comment) ? htmlspecialchars($players[$i]->comment) : '&nbsp;' ?></td>
    </tr>
  <?php endfor; ?>
</table>

</body>
</html>

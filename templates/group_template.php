<html>

<head>
<style type="text/css">
  table {
    border:2px solid #666;
    border-collapse: collapse;
    font-size: 14px;
    box-shadow: 3px 3px 12px rgba(0,0,0,0.3);
    text-align: center;
  }
  tr.header { background: linear-gradient(to bottom,#ddd,#eee,#ccc); }
  th { width:25px; padding: 6px 3px; border: 1px solid #666; }
  td { width:25px; height: 22px; padding-left: 5px; padding-right: 3px; border: 1px solid #666; }
  td.void { background-color: rgb(153, 153, 153); }
  td.mark { padding: 0; }
  th.small { font-size:small; }
  td.player { text-align:left; width: auto; }
  td.colored { background: #ffe; }
  td.mark a { display: block; height: 22px; line-height: 22px; text-decoration: none; color: black; font-size: 16px; }
  td.mark a:hover { background: #8f8; color: red;}
</style>
</head>

<body style="font-family: Verdana;">
<table>
  <tr class="header">
    <th>#</th>
    <th>Player</th>
    <th>R</th>
    <th class="small">Points</th>
    <th class="small">Rank</th>
    <?php for($i=0;$i<count($players);$i++): ?>
      <th><?php echo ($i+1); ?></th>
    <?php endfor; ?>
    <th>Result</th>
  <tr>
  <?php for($i=0;$i<count($players);$i++): ?>
    <tr>
      <td><?php echo ($i+1); ?></td>
      <td class="player"><?php echo $players[$i]->country_flag_s_tag(); ?> <?php echo htmlspecialchars($players[$i]->name); ?></td>
      <td><?php echo $players[$i]->rate; ?></td>
      <td class="colored"><?php echo $players[$i]->calculate_point(); ?></td>
      <td class="colored"><?php echo Util::ordinalize($player_ranks[$players[$i]->name]+1); ?></td>
      <?php for($j=0;$j<count($players);$j++): ?>
        <?php if($i == $j): ?>
          <td class="void">&nbsp;</td>
        <?php else: ?>
          <?php if($result = $players[$i]->get_result($players[$j])): ?>
            <td class="mark">
              <a target="_blank" href="http://81dojo.com/kifuviewer.html?kid=<?php echo $result->kifu_id; ?>"><?php echo $result->result_label_for($players[$i]); ?></a>
            </td>
          <?php else: ?>
            <td>&nbsp;</td>
          <?php endif ?>
        <?php endif; ?>
      <?php endfor; ?>
      <td class="colored"><?php echo isset($players[$i]->comment) ? htmlspecialchars($players[$i]->comment) : '&nbsp;' ?></td>
    </tr>
  <?php endfor; ?>
</table>

</body>
</html>

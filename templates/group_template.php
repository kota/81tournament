<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<style type="text/css">
  table.group {
    border-top:2px solid #666;
    border-left:2px solid #666;
    border-bottom:1px solid #666;
    border-right:1px solid #666;
    border-spacing: 0;
    font-size: 14px;
    box-shadow: 3px 3px 12px rgba(0,0,0,0.3);
    text-align: center;
    empty-cells: show;
  }
  th, td {
    border-top: 0;
    border-left: 0;
    border-bottom: 1px solid #666;
    border-right: 1px solid #666;
    width: 25px;
    padding-left: 3px;
    padding-right: 3px;
  }
  th { height: 30px; }
  td { height: 23px; }
  tr.header { background: linear-gradient(to bottom,#ddd,#eee,#ccc); background: -ms-linear-gradient(top,#ddd,#eee,#ccc); }
  th.small { font-size:small; }
  td.mark { padding: 0; font-family: Meiryo UI; }
  td.void { background-color: rgb(153, 153, 153); }
  td.flag { border-right: 0; }
  td.player { text-align:left; width: auto; padding-left: 0; }
  td.colored { background: #ffe; }
  td.mark a { display: block; height: 23px; line-height: 23px; text-decoration: none; color: black; font-size: 16px; }
  td.mark a:hover { background: #8f8; color: red;}
</style>
</head>

<body style="font-family: Verdana;">
<table class="group">
  <tr class="header">
    <th>#</th>
    <th colspan="2">Player</th>
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
      <td class="flag"><?php echo $players[$i]->country_flag_s_tag(); ?></td>
      <td class="player"><?php echo htmlspecialchars($players[$i]->name); ?></td>
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

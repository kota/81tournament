<html>

<head>
<style type="text/css">
  table, td, th { border: 1px solid; }
  td { width: 60px; }
  td.void { background-color: rgb(153, 153, 153);  }
</style>
</head>

<body>
<table style="border:2px solid;">
  <tr>
    <td></td>
    <?php foreach($players as $player): ?>
      <td><?php echo htmlspecialchars($player->name); ?></td>
    <?php endforeach; ?>
  <tr>
  <?php for($i=0;$i<count($players);$i++): ?>
    <tr>
      <td>
        <?php echo htmlspecialchars($players[$i]->name); ?>
      </td>
      <?php for($j=0;$j<count($players);$j++): ?>
        <?php if($i == $j): ?>
          <td class="void">&nbsp;</td>
        <?php else: ?>
          <?php $result = $player_results[$players[$i]->name] ?>
          <?php if($result && $result[$players[$j]->name]): ?>
            <td><?php echo $result[$players[$j]->name]; ?></td>
          <?php else: ?>
            <td>&nbsp;</td>
          <? endif ?>
        <?php endif; ?>
      <?php endfor; ?>
    </tr>
  <?php endfor; ?>
</table>

</body>
</html>

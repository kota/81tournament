<html>

<head>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery.bracket.min.js"></script>
<link rel="stylesheet" type="text/css" href="js/jquery.bracket.min.css" />
<script type="text/javascript">
</script>
<style type="text/css">
  table, td, th { border: 1px solid; }
  td { width: 60px; }
  td.void { background-color: rgb(153, 153, 153);  }
</style>
</head>

<body>
 <div id="single-elim">
   <script type="text/javascript">
   var tournament = {
       teams : [
         <?php for($i=0;$i<count($players)/2;$i++): ?>
           [<?php echo htmlspecialchars($players[$i*2]->to_json_string()) ?>,<?php echo htmlspecialchars($players[$i*2+1]->to_json_string()) ?>],
         <?php endfor; ?>
       ],
       results : [
         <?php foreach($rounds as $round): ?>
           [
           <?php foreach($round as $game): ?>
             <?php if(count($game) > 2): ?>
              <?php echo $game[2] ?>,
             <?php else: ?>
               null,
             <?php endif; ?>
           <?php endforeach; ?>
           ],
         <?php endforeach; ?>
       ]
   }

   function team_renderer(container, data, score) {
     if (data.country_code)
     {
       var zero_filled_code = ("00"+data.country_code).slice(-3);
       container.append('<img src="http://81dojo.com/dojo/images/flags_ss/'+zero_filled_code+'.png" /> ').append(data.name)
     } else {
       container.append(data.name);
     }
   }
 
   $(function() {
       $('#single-elim .diagram').bracket({
         skipConsolationRound: true,
         init: tournament, /* data to initialize the bracket with */
         decorator: {edit: function(){}, 
                     render: team_renderer}});
     })
   </script>
   <div class="diagram"/>
 </div>
 
</body>
</html>

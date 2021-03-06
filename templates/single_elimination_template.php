<!DOCTYPE html>
<html>

<head>
<!--[if ie]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><![endif]-->
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery.bracket.min.js"></script>
<link rel="stylesheet" type="text/css" href="js/jquery.bracket.min.css" />
<style type="text/css">
  table, td, th { border: 1px solid; }
  td { width: 60px; }
  td.void { background-color: rgb(153, 153, 153);  }
</style>
</head>

<body>
 <div id="single-elim">
  <script type="text/javascript">

  function onclick(data){
    if(data){
      window.open('http://81dojo.com/kifuviewer.html?kid=' + data);
    }
  }

   var tournament = {
       teams : [
         <?php for($i=0;$i<(int)count($players)/2;$i++): ?>
           [<?php echo htmlspecialchars($players[$i*2]->to_json_string()) . "," . htmlspecialchars($players[$i*2+1]->to_json_string()) ?>]<?php if($i != ((int)count($players)/2)-1) echo ",\n" ?>
         <?php endfor; ?>
       ],
       results : <?php echo $results; ?>
   }

   function team_renderer(container, data, score) {
     if (data.country_code)
     {
       var zero_filled_code = ("00"+data.country_code).slice(-3);
       container.append('<img src="http://81dojo.com/dojo/images/flags_ss/'+zero_filled_code+'.png" /> ').append(data.name)
     } else {
       if(data.name){
         container.append(data.name);
       } else {
         container.append(data);
       }
     }
   }
 
   $(function() {
       $('#single-elim .diagram').bracket({
         skipConsolationRound: true,
         init: tournament, /* data to initialize the bracket with */
         onMatchClick: onclick,
         decorator: {edit: function(){}, 
                     render: team_renderer}});
     })
   </script>
   <div class="diagram"/>
 </div>
 
</body>
</html>

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
 <script>
 $(function(){ 
 	$('.vote a').click(function(){
 	var idea = $(this).parent().parent().attr('id');
 	var opinion = $(this).attr('class');
 	opinion = opinion.split('-');
 	opinion = opinion[1];
 	idea = idea.split('-');
 	idea = idea[1];
 	var answer = opinion+ ';'+idea;
 	//alert(answer);
 		$.ajax({
                url: 'votes/cast_vote.php',
                type: "POST",
                data: answer,
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    alert(errorThrown);
                }, success: function(data, textStatus, XMLHttpRequest){
                 //   alert("vote saved");
                }
            });
 		
 		
 		});
 	return false;	
 });
 </script>
<?php
$a = $_POST['page'];

?>
<html>
	<head>
		
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

		<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
		<style>
			a {

			
		   }

		   a.page-link:hover, div.parse:hover {

		   cursor:pointer;}
		</style>
	</head>
<body>
	<nav class="navbar navbar-light bg-light">
	  <a class="navbar-brand" href="#">Home</a>
	  <button class="btn btn-primary" id="parse" type="button">Парсить</button>
	</nav>
	<div class="container">
		<div class="row">

				<form method="post" id="ajax_form" action="">
					<input type="hidden" name="page" id="page"/>
					<input type="hidden" name="allpage" id="allpage"/>
					<input type="hidden" id="action" name="action" class="action" />
					<!--<input type="button" id="btn" value="Отправить" />-->

				</form>
		</div>
	</div>
	
	<div class="container">
		<div class="row">
			<div id="result_form2"></div> 
			<div id="result_form"></div> 
		</div>
		
		<div id="paginator"></div>
	</div>	
		
	
	<script>
	

	
$( document ).ready(function() {
	if ($("#page").val() == '' || $("#page").val() < 1 )
	{ 			$("#page").val(1);		}
	if ($("#allpage").val() == '' || $("#allpage").val() < 1 )
	{ 			$("#allpage").val(1);		}
	$("#action").val('');


	//showDescription('result_form2', 70, 'descr.php');
	sendAjaxForm('result_form', 'ajax_form', 'core.php');

		
	 
	function sendAjaxForm(result_form, ajax_form, url) {
		$.ajax({
			url:     url, 
			type:     "POST", //метод отправки
			dataType: "html", //формат данных
			data: $("#"+ajax_form).serialize(),  // Сеарилизуем объект
			success: function(response) { //Данные отправлены успешно
				result = $.parseJSON(response);
				var text = '';
				for (var j=0; j < result.posts.count; j++){
				
					text +='<div class="card">  <div class="card-body"><a target="_blank" href="https://habr.com/ru/post/'+result.posts[j].post_id+'/"><h4>'+result.posts[j].id+'. '+result.posts[j].title+'</h4></a>'+result.posts[j].short+'';
			
					text += '<br /><button class="podrobno" id="" v="'+result.posts[j].id+'">Подробнее</button>';
					
					text += '</div></div>';
					
				}
				$('#result_form').html(text);
				
				var res = '';
				res += '<nav aria-label="Page navigation example"><ul class="pagination">';
				
				
				
				if (result.page > 1) {
					res += '<li class="page-item"><a id="anc-1" class="page-link" v="1"> << </a></li> ';		
				}
				if (result.page > 1) {
					res += '<li class="page-item"><a id="anc-'+ (result.page-1) +'" class="page-link" v="'+ (result.page-1) +'"> < </a></li> ';		
				}
				for (var i=1; i <= result.allpage; i++){
					
					if (i == result.page){
						res += ' <li class="page-item active" aria-current="page"><a class="page-link" v="'+i+'">' + i + '<span class="sr-only">(current)</span></a></li>';
					}else {
						res += '<li class="page-item"><a id="anc-'+i+'" class="page-link" v="'+i+'">'+ i + '</a></li> ';
					}	
				}
				
				if (result.page < result.allpage) {
					res += '<li class="page-item"><a id="anc-'+ ( Number(result.page)+1) +'" class="page-link" v="'+ ( Number(result.page)+1) +'"> > </a></li> ';		
				}
				if (result.page < result.allpage) {
					res += '<li class="page-item"><a id="anc-1" class="page-link" v="'+result.allpage+'"> >> </a></li> ';		
				}
				 res += ' </ul>	</nav>';
				
				$('#paginator').html(res);	
				
				$("#action").val('');
				$("#page").val(result.page);
				
				$('.page-link').click(function() {
				var p = ($(this).attr('v')); 
				$("#page").val(p);
				sendAjaxForm('result_form', 'ajax_form', 'core.php');
		});
				
			},
			error: function(response) { // Данные не отправлены
				$('#result_form').html('Ошибка. Данные не отправлены.');
			}
			
		});
	}
	
	
	
	function showDescription(result_form2, id, url) {
		$.ajax({
			url:     url, 
			type:     "POST", //метод отправки
			dataType: "json", //формат данных
			data: {id:id},  // Сеарилизуем объект
			success: function(response) { //Данные отправлены успешно
				des = $.parseJSON(response);
					text ='<div class="card"> '+des.description+'</div>'
				$('#result_form2').html(text);
		
				
			},
			error: function(response) { // Данные не отправлены
				$('#result_form2').html('Ошибка. Данные не отправлены.');
			}
			
		});
	}
	
		$('#parse').click(function() {
		   $("#action").val('parse');
		   
		   sendAjaxForm('result_form', 'ajax_form', 'core.php');
		});
		
		
		//$('.podrobno').click(function() { ????
		//Document.getElementsByClassName
		// $('.podrobno').onClick(function () {
			
		
			
			// var btn = document.getElementsByClassName("podrobno")[0];
			// btn.Click(function () {
		// var id = ($(this).attr('v'));  
		   // alert(id);
		   // //showDescription('result_form2', id, 'descr.php');
		//});


	
});
	$(document).ready(function() {
			
		$('#result_form .podrobno').on('click', function(e){
			alert($(this).attr('v'));
		});	   
	});	
	</script>
	
</body>
</html>
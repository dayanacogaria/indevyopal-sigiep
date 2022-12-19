<?php
function __autoload($class_name)
{
	require_once 'tree.php';
}

$tree = new Tree();

$elements = $tree->get();
$masters = $elements["masters"];
$childrens = $elements["childrens"];

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<style type="text/css">
	ul{
		list-style: none;
	}
       
	</style>
	<script>
	$(document).ready(function()
	{
		$(".btn-folder").on("click", function(e)
		{
			e.preventDefault();
			if($(this).attr("data-status") === "1")
			{
				$(this).attr("data-status", "0");
				$(this).find("span").removeClass("glyphicon-minus-sign").addClass("glyphicon-plus-sign")
			}
			else
			{
				$(this).attr("data-status", "1");
				$(this).find("span").removeClass("glyphicon-plus-sign").addClass("glyphicon-minus-sign")
			}
			$(this).next("ul").slideToggle();
		})
	});
	</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-10" id="nested" style="background: #222; color: #ddd">
				<h3 class="heading text-center">Tree</h3><hr>
				<ul>
				<?php
				foreach($masters as $master)
				{
				?>
					<li style="margin: 5px 0px">
                                            <span ><i class='glyphicon glyphicon-folder-open'></i></span>
						<a href="#" data-status="<?php echo count($master["flujo_si"]) ?>"
						style="margin: 5px 6px" class="btn btn-primary sombra btn-folder">
						<span style="height:20px" class='<?php echo $master['flujo_si'] > 1 ? 
							'glyphicon glyphicon-minus-sign' :
							'glyphicon glyphicon-plus-sign' ?>'
						</span> 
						<?php echo ucwords(strtolower($master["nombre1"])); ?></a>
                                                <?php echo Tree::nested($childrens, $master["flujo_si"]) ?>
					</li>
				<?php
				}
				?>	
				</ul>
			</div>
		</div>
	</div>
</body>
</html>
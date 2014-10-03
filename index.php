<?php
set_time_limit(9999999999999999);
if(isset($_POST["go"])){
	include("class.php");
	$ids = new getUrl($_POST['url']);
	$ids->getAllMembers();
	$ids->insertToDb();
	if($ids->tableIsset==false) 
	{ 
		echo "Creating a new table (in db) for this community:<br>";
		foreach($ids->newMembersArray as $newMember) 
		{
		?>
		<table border="1" cellpadding="5">
			<tr>
				<td><?php echo $newMember['first_name']." ".$newMember['last_name']; ?></td>
				<td><?php echo "http://vk.com/id".$newMember['uid']; ?></td>
				<td><?php echo date("Y-m-d H:i:s"); ?></td>
			</tr>
		</table>
		<?php 
		}
	}
	else 
	{ 
		echo "this community has been updated:<br>"; 
		foreach($ids->newMembersArray as $newMember) 
		{
		?>
		<table border="1" cellpadding="5">
			<tr>
				<td><?php echo $newMember['first_name']." ".$newMember['last_name']; ?></td>
				<td><?php echo "http://vk.com/id".$newMember['uid']; ?></td>
				<td><?php echo date("Y-m-d H:i:s"); ?></td>
			</tr>
		</table>
		<?php 
		}
		echo "now, old users:<br>"; 
		foreach($ids->oldMembersArray as $oldMember) 
		{
		?>
		<table border="1" cellpadding="5">
			<tr>
				<td><?php echo $oldMember['first_name']." ".$oldMember['last_name']; ?></td>
				<td><?php echo "http://vk.com/id".$oldMember['uid']; ?></td>
				<td><?php echo $oldMember['date']; ?></td>
			</tr>
		</table>
		<?php 
		}
	}
}
?>

<form method="POST">
	Insert your URL into the <input type = "text" name ="url">
	<input type = "submit" name ="go" value="scrap data!">
</form>

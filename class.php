<?php
include("dbSettings.php");
class getUrl extends mysqlConnect
{
	var $groupId = 1;
	var $membersArray = array();
	var $newMembersArray = array();
	var $oldMembersArray = array();
	var $tableIsset = true;
	
	function getUrl($url)
	{
		$this->link = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
		preg_match_all("%.*vk\.com/search\?c\[section\]=people\&c\[group\]=(.*)%", $url, $result);
		if(!mysqli_query($this->link, "SELECT * FROM `".$result[1][0]."`"))
		{
			mysqli_query($this->link, "CREATE TABLE `".$result[1][0]."` (
		 	`id` int(128) NOT NULL AUTO_INCREMENT,
		 	`name` varchar(128) NOT NULL,
		 	`url` varchar(128) NOT NULL,
		 	`date` datetime NOT NULL,
		 	`status` int(128) NOT NULL,
		 	PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4904 ;");
			$this->tableIsset = false;
		}
		
		$this->groupId = $result[1][0];
	}
	
	function getAllMembers()
	{
		$response = json_decode(file_get_contents("https://api.vk.com/method/groups.getMembers?group_id=".$this->groupId), true);
		$membersResultArray = array();
		for($i=0; $i<$response["response"]["count"]; $i=$i+1000)
		{
			$members =json_decode(file_get_contents("https://api.vk.com/method/groups.getMembers?fields=first_name&group_id=".$this->groupId."&offset=".$i), true);
			$membersResultArray = array_merge($membersResultArray, $members["response"]["users"]);
		}
		$this->membersArray = $membersResultArray;
	}
	
	function insertToDb()
	{
		mysqli_query($this->link, "UPDATE `".$this->groupId."` SET `status` = '0'");
		$oldI=0;
		$newI=0;
		foreach($this->membersArray as $oneMemberArray)
		{	
			$oldMembersFromDb = mysqli_query($this->link, "SELECT * FROM `".$this->groupId."` WHERE `url` = 'http://vk.com/id".$oneMemberArray['uid']."'");
			if(mysqli_num_rows($oldMembersFromDb)==0)
			{
				mysqli_query($this->link, "INSERT INTO `".$this->groupId."` (`id`, `name`, `url`, `date`, `status`) VALUES (NULL, '".str_replace("'", "", $oneMemberArray['first_name'])." ".str_replace("'", "", $oneMemberArray['last_name'])."', 'http://vk.com/id".$oneMemberArray['uid']."', '".date("Y-m-d H:i:s")."', '1');");
				$this->newMembersArray[$newI] = $oneMemberArray;
				$newI++;
			}
			else
			{
				$oldMemberVar = mysqli_fetch_assoc($oldMembersFromDb);
				$this->oldMembersArray[$oldI] = $oneMemberArray;
				$this->oldMembersArray[$oldI]["date"] = $oldMemberVar["date"];
				$oldI++;
			}
		}
	}

}
?>

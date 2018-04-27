<?php
	$connection=mysqli_connect('localhost','root','p0o9i8u7','rest_api');

	$request_method=$_SERVER["REQUEST_METHOD"];
	switch($request_method)
	{
		case 'GET':
			//Retrieve tasks
			if(!empty($_GET["task_id"]))
			{
				$task_id=intval($_GET["task_id"]);
				get_tasks($task_id);
			}
			else
			{
				get_tasks();
			}
			break;
		case 'POST':
			// Insert Task
			insert_task();
			break;
		case 'PUT':
			// Update Task
			$task_id=intval($_GET["task_id"]);
			update_task($task_id);
			break;
		case 'DELETE':
			// Delete Task
			$task_id=intval($_GET["task_id"]);
			delete_task($task_id);
			break;
		default:
			// Invalid Request Method
			header("HTTP/1.0 405 Method Not Allowed");
			break;
	}
	
	function insert_task()
	{
		global $connection;
		$task_name=$_POST["name"];
		$query="INSERT INTO tasks SET name='{$task_name}'";
		if(mysqli_query($connection, $query))
		{
			$response=array(
				'status' => 1,
				'status_message' =>'Task Added Successfully.'
			);
		}
		else
		{
			$response=array(
				'status' => 0,
				'status_message' =>'Task Addition Failed.'
			);
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}

	function get_tasks($task_id=0)
	{
		global $connection;
		$query="SELECT * FROM tasks";
		if($task_id != 0)
		{
			$query.=" WHERE id=".$task_id." LIMIT 1";
		}
		$response=array();
		$result=mysqli_query($connection, $query);
		while($row=mysqli_fetch_array($result))
		{
			$response[]=array('name' => $row['name'], 'id' => $row['id']);
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}

	function delete_task($task_id)
	{
		global $connection;
		$query="DELETE FROM tasks WHERE id=".$task_id;
		if(mysqli_query($connection, $query))
		{
			$response=array(
				'status' => 1,
				'status_message' =>'Task Deleted Successfully.'
			);
		}
		else
		{
			$response=array(
				'status' => 0,
				'status_message' =>'Task Deletion Failed.'
			);
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}

	function update_task($task_id)
	{
		global $connection;
		parse_str(file_get_contents("php://input"),$post_vars);
		$task_name=$post_vars["task_name"];
		$query="UPDATE tasks SET task_name='{$task_name}' WHERE id=".$task_id;
		if(mysqli_query($connection, $query))
		{
			$response=array(
				'status' => 1,
				'status_message' =>'Task Updated Successfully.'
			);
		}
		else
		{
			$response=array(
				'status' => 0,
				'status_message' =>'Task Updation Failed.'
			);
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}

	// Close database connection
	mysqli_close($connection);
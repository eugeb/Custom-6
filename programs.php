<?php
require "config.php";
 
 if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
 
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
 
        exit(0);
    }
    
    $postdata = file_get_contents("php://input");
    // $postdata = "a";
	 if (isset($postdata) && !empty($postdata)) {
         $request = json_decode($postdata);
         $role_id = $request->role_id;
         $userid = $request->userid;
         $is_completed = $request->is_completed;
         $starttime = $request->starttime;
         $endtime = $request->endtime;
		 /*$role_id = 1;
		 $userid = 1;
		 $is_completed = true;
		 $starttime = '0000-00-00';
		 $endtime =  '0000-00-00';*/
         if ($role_id != "") {
            // Create connection
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
			$sql = "";
            if($role_id ==1){
			$sql = "SELECT id as courseid, 
			course_name, 
			course_description, 
			creator_id, 
			creation_datetime, course_complete, course_start_date, course_end_date, course_cost, course_pass_mark, venue, pretest_enabled, exams_enabled, trainerid, rating, preview_image, employee_segment, employee_level, client, pdf_url, cvs_url FROM course ";
            }else if($role_id == 2){
			$sql = "SELECT course.id as courseid, course_name, course_description, creator_id, creation_datetime, course_complete, course_start_date, course_end_date, course_cost, course_pass_mark, venue, pretest_enabled, exams_enabled, trainerid, rating, preview_image, employee_segment, employee_level, client, pdf_url, cvs_url FROM course INNER JOIN users_programs ON users_programs.programid = course.id and users_programs.userid = '$userid' ";
            }
			
			if($is_completed==false){
				$sql .=" WHERE course_end_date >= DATE_FORMAT(NOW(),'%Y-%m-%d')";
            }else{
				$sql .=" WHERE course_end_date < DATE_FORMAT(NOW(),'%Y-%m-%d')";
			}
			
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $r2= array();
				
                while($row = $result->fetch_assoc()) {
                    $r=array("course_description"=>utf8_decode($row["course_description"])
                    ,"courseid"=>utf8_decode($row["courseid"])
                    ,"course_name"=>utf8_decode($row["course_name"])
                    ,"course_start_date"=>utf8_decode($row["course_start_date"])
                    ,"course_end_date"=>utf8_decode($row["course_end_date"])
                    ,"venue"=>utf8_decode($row["venue"])
                    ,"course_cost"=>utf8_decode($row["course_cost"])
                    ,"course_pass_mark"=> utf8_decode($row["course_pass_mark"])
                    ,"exams_enabled"=>utf8_decode($row["exams_enabled"])
                    ,"pretest_enabled"=>utf8_decode($row["pretest_enabled"])
                    ,"trainerid"=>utf8_decode($row["trainerid"])
                    ,"preview_image"=>utf8_decode($row["preview_image"])
                    );
					/*$cid = $row["courseid"];
					$r_2 = array();
					$cuserid = $userid;
					$sql_exams ="SELECT exams.examid, exams.courseid, exams.isfinal, exams.unitid, exams.startdate, exams.enddate,exams.description ,(select count(resultid) from results_exam_user where results_exam_user.examid = exams.examid and results_exam_user.userid = '$cuserid') as iscompleted FROM exams WHERE exams.courseid ='$cid'";
					$result_exams = $conn->query($sql_exams);
					if ($result_exams->num_rows > 0) {
						while($row_exams = $result_exams->fetch_assoc()) {
							$r_2=array("examid"=>utf8_decode($row_exams["examid"])
								,"courseid"=>utf8_decode($row_exams["courseid"])
								,"description"=>utf8_decode($row_exams["description"])
								,"isfinal"=>utf8_decode($row_exams["isfinal"])
								,"unitid"=>utf8_decode($row_exams["unitid"])
								,"startdate"=>utf8_decode($row_exams["startdate"])
								,"enddate"=>utf8_decode($row_exams["enddate"])
								,"iscompleted"=>utf8_decode($row_exams["iscompleted"])
								);
							array_push($r["exams"],$r_2);
						}						
					}*/
					array_push($r2,$r);
                }
				
                echo json_encode($r2, JSON_UNESCAPED_UNICODE);
            } else {
                echo "{}";
            }
            $conn->close();
         }else {
         echo "Empty username parameter!";
         }
     }else {
        echo "Not called properly with username parameter!";
     }
?>
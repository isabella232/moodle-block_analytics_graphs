<?php
require_once('../../config.php');
include("lib.php");
require_once($CFG->dirroot.'/lib/moodlelib.php');

$studentid = required_param('student_id', PARAM_INT);
$courseid = required_param('course_id', PARAM_INT);

/* Access control */
require_login($courseid);
$context = context_course::instance($courseid);
require_capability('block/analytics_graphs:viewpages', $context);

$resource_access =  block_analytics_graphs_get_user_resource_url_page_access($courseid, $studentid, 0);
$assign_info = block_analytics_graphs_get_user_assign_submission($courseid, $studentid);

echo json_encode(array("resources"=>$resource_access, "assign"=>$assign_info));
?>
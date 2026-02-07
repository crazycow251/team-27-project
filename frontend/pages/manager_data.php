<?php
require_once '../../database/config.php';
session_start();

//Temporary manager login
$manager_id = 100001;

//ADD PROJECT HANDLER
if (isset($_POST['add_project'])) {

    $project_name = trim($_POST['project_name']);
    $team = $_POST['team'] ?? [];
    $team_leader = $_POST['team_leader'] ?? null;

    if ($project_name === '') {
        die("Project name cannot be empty");
    }

    if (empty($team)) {
        die("At least one team member must be selected");
    }

    //Insert project
    $stmt = $conn->prepare(
        "INSERT INTO projects (project_name, manager_id) VALUES (?, ?)"
    );
    $stmt->bind_param("si", $project_name, $manager_id);
    $stmt->execute();

    $project_id = $conn->insert_id;

    //Assigns team members
    $stmt = $conn->prepare(
        "INSERT INTO team_members (project_id, employee_id, role)
         VALUES (?, ?, ?)"
    );

    foreach ($team as $employee_id) {
        $role = ($employee_id == $team_leader) ? "Team Leader" : "Member";
        $stmt->bind_param("iis", $project_id, $employee_id, $role);
        $stmt->execute();
    }

    header("Location: manager.php");
    exit;
}

//Dashboard counts

//Total employees
$q1 = $conn->prepare("SELECT COUNT(*) AS total FROM employee_login");
$q1->execute();
$totalEmployees = $q1->get_result()->fetch_assoc()['total'];

//Total projects for manager
$q2 = $conn->prepare(
    "SELECT COUNT(*) AS total FROM projects WHERE manager_id = ?"
);
$q2->bind_param("i", $manager_id);
$q2->execute();
$totalProjects = $q2->get_result()->fetch_assoc()['total'];

//Total tasks for manager projects
$q3 = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM tasks
     WHERE project_id IN (
        SELECT project_id FROM projects WHERE manager_id = ?
     )"
);
$q3->bind_param("i", $manager_id);
$q3->execute();
$totalTasks = $q3->get_result()->fetch_assoc()['total'];

//Project summary bit
$projectSummary = $conn->prepare(
    "SELECT
        p.project_id,
        p.project_name,
        COUNT(t.task_id) AS total_tasks,
        SUM(t.status = 'Completed') AS completed_tasks
     FROM projects p
     LEFT JOIN tasks t ON p.project_id = t.project_id
     WHERE p.manager_id = ?
     GROUP BY p.project_id"
);
$projectSummary->bind_param("i", $manager_id);
$projectSummary->execute();
$projectRows = $projectSummary->get_result();

//Upcoming tasks
$upcoming = $conn->prepare(
    "SELECT
        t.task_name,
        t.due_date,
        p.project_name,
        e.name AS employee_name
     FROM tasks t
     JOIN projects p ON t.project_id = p.project_id
     JOIN employee_login e ON t.assigned_to = e.staff_id
     WHERE p.manager_id = ?
     ORDER BY t.due_date ASC"
);
$upcoming->bind_param("i", $manager_id);
$upcoming->execute();
$upcomingTasks = $upcoming->get_result();

//All employees shown
$users = $conn->query(
    "SELECT staff_id, name FROM employee_login ORDER BY name"
);

//Chart data 

$chartLabels = [];
$chartData = [];

$ret = $conn->prepare(
    "SELECT
        p.project_name,
        ROUND(
            IF(COUNT(t.task_id) = 0, 0,
               (SUM(t.status = 'Completed') / COUNT(t.task_id)) * 100),
            0
        ) AS completion_percent
     FROM projects p
     LEFT JOIN tasks t ON p.project_id = t.project_id
     WHERE p.manager_id = ?
     GROUP BY p.project_id"
);
$ret->bind_param("i", $manager_id);
$ret->execute();
$result = $ret->get_result();

while ($row = $result->fetch_assoc()) {
    $chartLabels[] = $row['project_name'];
    $chartData[] = (int) $row['completion_percent'];
}

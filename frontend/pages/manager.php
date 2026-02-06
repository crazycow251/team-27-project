<?php
require_once '../../database/config.php';
session_start();

$manager_id = 100001;

//Handle add project form
if (isset($_POST['add_project'])) {

    $project_name = $_POST['project_name'];
    $team = $_POST['team'] ?? [];
    $team_leader = $_POST['team_leader'] ?? null;

    if (empty(trim($project_name))) {
        die("Project name cannot be empty");
    }

    if (empty($team)) {
        die("At least one team member must be selected");
    }

    //Inserts project
    $stmt = $conn->prepare(
        "INSERT INTO projects (project_name, manager_id) VALUES (?, ?)"
    );
    $stmt->bind_param("si", $project_name, $manager_id);
    $stmt->execute();

    $project_id = $conn->insert_id;

    //Assigns a team members to team_members table
    $stmt = $conn->prepare(
        "INSERT INTO team_members (project_id, employee_id, role) VALUES (?, ?, ?)"
    );

    //Assigns role
    foreach ($team as $employee_id) {

        $role = ($employee_id == $team_leader) ? "Team Leader" : "Member";

        $stmt->bind_param("iis", $project_id, $employee_id, $role);
        $stmt->execute();
    }

    // Prevents resubmission of task
    header("Location: manager.php");
    exit;
}

/*Gets total employees*/
$q1 = $conn->prepare("SELECT COUNT(*) AS total_employees FROM employee_login");
$q1->execute();
$totalEmployees = $q1->get_result()->fetch_assoc()['total_employees'];


//Gets total prpojects for this manager
$q2 = $conn->prepare("SELECT COUNT(*) AS total_projects FROM projects WHERE manager_id = ?");
$q2->bind_param("i", $manager_id);
$q2->execute();
$totalProjects = $q2->get_result()->fetch_assoc()['total_projects'];


//Gets total task for manager project
$q3 = $conn->prepare("SELECT COUNT(*) AS total_tasks 
                      FROM tasks 
                      WHERE project_id IN (SELECT project_id FROM projects WHERE manager_id = ?)");
$q3->bind_param("i", $manager_id);
$q3->execute();
$totalTasks = $q3->get_result()->fetch_assoc()['total_tasks'];


//Gets project summary tables
$projectSummary = $conn->prepare("
    SELECT 
        p.project_id,
        p.project_name,
        COUNT(t.task_id) AS total_tasks,
        SUM(t.status = 'Completed') AS completed_tasks
    FROM projects p
    LEFT JOIN tasks t ON p.project_id = t.project_id
    WHERE p.manager_id = ?
    GROUP BY p.project_id
");
$projectSummary->bind_param("i", $manager_id);
$projectSummary->execute();
$projectRows = $projectSummary->get_result();


//Gets upcoming tasks ordered by due date
$upcoming = $conn->prepare("
    SELECT t.*, p.project_name, e.name AS employee_name
    FROM tasks t
    JOIN projects p ON t.project_id = p.project_id
    JOIN employee_login e ON t.assigned_to = e.staff_id
    WHERE p.manager_id = ?
    ORDER BY t.due_date ASC
");
$upcoming->bind_param("i", $manager_id);
$upcoming->execute();
$upcomingTasks = $upcoming->get_result();

// All users
$users = $conn->query("SELECT staff_id, name FROM employee_login");



//Chart data
$chartLabels = [];
$chartData = [];

$ret = $conn->prepare("
    SELECT p.project_name,
           ROUND(
  IF(COUNT(t.task_id) = 0, 0,
     (SUM(t.status = 'Completed') / COUNT(t.task_id)) * 100),
  0
) AS completion_percent

    FROM projects p
    LEFT JOIN tasks t ON p.project_id = t.project_id
    WHERE p.manager_id = ?
    GROUP BY p.project_id
");
$ret->bind_param("i", $manager_id);
$ret->execute();
$chartRes = $ret->get_result();

while ($row = $chartRes->fetch_assoc()) {
    $chartLabels[] = $row['project_name'];
    $chartData[] = $row['completion_percent'] ?? 0;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Make-It-All | Manager Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="/frontend/styles/style.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="p-4 bg-light d-flex flex-column min-vh-100">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg" style="background-color: rgb(232,196,104);">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="../../assets/MakeItAllLogo.png" height="40" class="me-2"> 
        Make-It-All Ltd
      </a>
    </div>
  </nav>

  <div class="container my-4">
    <h1 class="mb-4">Manager Dashboard</h1>

<div class="d-flex justify-content-end mb-3">
  <button 
    class="btn btn-primary"
    data-bs-toggle="modal"
    data-bs-target="#addProjectModal">
    + Add New Project
  </button>
</div>


    <!-- Summary Row -->
    <div class="row dashboard-projects mb-4">
      <div class="col-md-4 mb-3">
        <div class="dashboard-project-box blue p-3 rounded shadow-sm text-center">
          <h2>Total Employees</h2>
          <h3><?= $totalEmployees ?></h3>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="dashboard-project-box green p-3 rounded shadow-sm text-center">
          <h2>Total Projects</h2>
          <h3><?= $totalProjects ?></h3>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="dashboard-project-box red p-3 rounded shadow-sm text-center">
          <h2>Total Tasks</h2>
          <h3><?= $totalTasks ?></h3>
        </div>
      </div>
    </div>

<div class="modal fade" id="addProjectModal">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Create New Project</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <label class="form-label">Project Name</label>
        <input type="text" name="project_name" class="form-control" required>

        <<label class="form-label mt-3">Assign Team Members</label>
<?php while ($u = $users->fetch_assoc()): ?>
  <div class="form-check d-flex align-items-center justify-content-between">
    <div>
      <input class="form-check-input" type="checkbox" name="team[]" value="<?= $u['staff_id'] ?>" id="staff-<?= $u['staff_id'] ?>">
      <label class="form-check-label" for="staff-<?= $u['staff_id'] ?>"><?= $u['name'] ?></label>
    </div>
    <div>
      <input class="form-check-input" type="radio" name="team_leader" value="<?= $u['staff_id'] ?>">
      <label class="form-check-label">Team Leader</label>
    </div>
  </div>
<?php endwhile; ?>


      </div>

      <div class="modal-footer">
        <button type="submit" name="add_project" class="btn btn-success">
          Create Project
        </button>
      </div>

    </form>
  </div>
</div>


    //Project Summary Table 
    <h5 class="mb-3">Project Summary</h5>
    <div class="table-responsive mb-4">
      <table class="table table-bordered table-striped">
        <thead class="table-light">
          <tr>
            <th>Project</th>
            <th>Tasks</th>
            <th>Completed</th>
            <th>Completion %</th>
          </tr>
        </thead>
        <tbody>

        <?php while ($p = $projectRows->fetch_assoc()): ?>
          <?php
            $percent = ($p['total_tasks'] > 0) 
              ? round(($p['completed_tasks'] / $p['total_tasks']) * 100)
              : 0;
          ?>
          <tr>
            <td><?= $p['project_name'] ?></td>
            <td><?= $p['total_tasks'] ?></td>
            <td><?= $p['completed_tasks'] ?></td>
            <td><?= $percent ?>%</td>
          </tr>
        <?php endwhile; ?>

        </tbody>
      </table>
    </div>

    //Upcoming Tasks
    <h5>Upcoming Tasks (Soonest First)</h5>
    <ul class="list-group mb-4">

    <?php while ($t = $upcomingTasks->fetch_assoc()): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
        <div>
          <strong><?= $t['task_name'] ?></strong>
          <small class="text-muted">(<?= $t['project_name'] ?>)</small><br>
          <small>Due: <?= $t['due_date'] ?></small>
        </div>
        <small>Assigned: <?= $t['employee_name'] ?></small>
      </li>
    <?php endwhile; ?>

    </ul>

    //Chart 
    <h5>Project Completion Overview</h5>
    <canvas id="managerChart"></canvas>

  </div>

<script>
const ctx = document.getElementById("managerChart");

new Chart(ctx, {
  type: "line",
  data: {
    labels: <?= json_encode($chartLabels) ?>,
    datasets: [{
      label: "Completion %",
      data: <?= json_encode($chartData) ?>,
      borderWidth: 2,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        max: 100,
        title: {
          display: true,
          text: "Completion (%)"
        }
      },
      x: {
        title: {
          display: true,
          text: "Projects"
        }
      }
    }
  }
});
</script>


</body>
</html>
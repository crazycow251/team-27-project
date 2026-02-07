<?php require_once 'manager_data.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Make-It-All | Manager Dashboard</title>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <link rel="stylesheet" href="../../frontend/styles/style.css">

</head>
<body class="p-4 bg-light">

  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand" href="../../index.html"><img src="../../assets/MakeItAllLogo.png" width="30" height="30" alt=""></a>
        <a class="navbar-brand" href="../../index.html">Make-It-All Ltd</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
          <li class="nav-item active"><a href="login.html" class="nav-btn">Login</a></li>
          <li class="nav-item active"><a href="register.html" class="nav-btn">Register</a></li>
          <li class="nav-item active"><a href="../../index.html" class="nav-btn">Home</a></li>
          <li class="nav-item active"><a href="employee.html" class="nav-btn">Dashboards</a></li>
      </ul>
    </div>
  </nav>

  <div class="container">
    <h1 id="dashboard-dash">Manager Dashboard</h1>

    <div class="dashboard-projects">
        <div class="dashboard-project-box blue">
            <div class="dashboard-project-content">
                <h2>Total Employees</h2>
                <h3 id="blue"><?= $totalEmployees ?></h3>
            </div>
        </div>
        <div class="dashboard-project-box green">
            <div class="dashboard-project-content">
                <h2>Total Projects</h2>
                <h3 id="green"><?= $totalProjects ?></h3>
            </div>
        </div>
        <div class="dashboard-project-box red">
            <div class="dashboard-project-content">
                <h2>Total Tasks</h2>
                <h3 id="red"><?= $totalTasks ?></h3>
            </div>
        </div>
    </div>

    <h5 id="manager-table-title" class="mb-3">Project Summary</h5>
    <table id="manager-table" class="table table-bordered table-striped mb-4">
      <thead>
        <tr>
          <th>Project</th>
          <th>Team Members</th>
          <th>Tasks</th>
          <th>Completed</th>
          <th>Completion %</th>
        </tr>
      </thead>
      <tbody>
<?php while ($row = $projectRows->fetch_assoc()): 
    $percent = $row['total_tasks'] == 0
        ? 0
        : round(($row['completed_tasks'] / $row['total_tasks']) * 100);
?>
<tr>
  <td><?= htmlspecialchars($row['project_name']) ?></td>
  <td><?= $row['total_tasks'] ?></td>
  <td><?= $row['completed_tasks'] ?></td>
  <td><?= $percent ?>%</td>
</tr>
<?php endwhile; ?>
</tbody>

    </table>

    <div id="manager-tasks" class="mb-4">
      <h5>Upcoming Tasks (Next Due First)</h5>
      <ul class="list-group">
<?php while ($task = $upcomingTasks->fetch_assoc()): ?>
<li class="list-group-item d-flex justify-content-between align-items-center">
  <div>
    <strong><?= htmlspecialchars($task['task_name']) ?></strong>
    <small class="text-muted">(<?= htmlspecialchars($task['project_name']) ?>)</small><br>
    <small>Due: <?= $task['due_date'] ?></small>
  </div>
  <small>Assigned to: <?= htmlspecialchars($task['employee_name']) ?></small>
</li>
<?php endwhile; ?>
</ul>

    </div>

    <div id="manager-chart" class="mb-4">
      <h5>Project Completion Overview</h5>
      <canvas id="managerChart"></canvas>
    </div>
  </div>

  <div class="footer text-center mt-auto">
    <div class="container">
      <p>Make-It-All Ltd | Team 27 Project</p>
    </div>
  </div>

  <script>
const ctx = document.getElementById("managerChart");

new Chart(ctx, {
  type: "bar",
  data: {
    labels: <?= json_encode($chartLabels) ?>,
    datasets: [{
      label: "% Completion",
      data: <?= json_encode($chartData) ?>,
      backgroundColor: "#198754"
    }]
  },
  options: {
    scales: {
      y: { beginAtZero: true, max: 100 }
    }
  }
});
</script>

</body>
</html>

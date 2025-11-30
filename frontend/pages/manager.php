<?php
//Tasks: 
// Start session if you want to check login later
// "session_start();"
// Add these from database 
// $employees = ...;
// $projects = ...;
// $tasks = ...;
?>
<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Make-It-All | Manager Dashboard</title>

  <!-- CDNs -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <!-- Style Sheet -->
  <link rel="stylesheet" href="/frontend/styles/style.css">

</head>
<body class="p-4 bg-light">

  <!-- Navbar -->
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand" href="/index.php">
          <img src="/assets/MakeItAllLogo.png" width="30" height="30" alt="">
        </a>
        <a class="navbar-brand" href="/index.php">Make-It-All Ltd</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li class="nav-item active"><a href="/frontend/pages/login.php" class="nav-btn">Login</a></li>
        <li class="nav-item active"><a href="/frontend/pages/register.php" class="nav-btn">Register</a></li>
        <li class="nav-item active"><a href="/index.php" class="nav-btn">Home</a></li>
        <li class="nav-item active"><a href="/frontend/pages/employee.php" class="nav-btn">Dashboards</a></li>
      </ul>
    </div>
  </nav>

  <div class="container">
    <h1 id="dashboard-dash">Manager Dashboard</h1>

    <!-- Summary Row -->
    <div class="dashboard-projects">
        <div class="dashboard-project-box blue">
            <div class="dashboard-project-content">
                <h2>Total Employees</h2>
                <h3 id="blue">3</h3>
            </div>
        </div>
        <div class="dashboard-project-box green">
            <div class="dashboard-project-content">
                <h2>Total Projects</h2>
                <h3 id="green">3</h3>
            </div>
        </div>
        <div class="dashboard-project-box red">
            <div class="dashboard-project-content">
                <h2>Total Tasks</h2>
                <h3 id="red">5</h3>
            </div>
        </div>
    </div>

    <!-- Project Summary -->
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
        <tr>
          <td>Project Alpha</td>
          <td>Alice, Ben</td>
          <td>2</td>
          <td>1</td>
          <td>50%</td>
        </tr>
        <tr>
          <td>Project Beta</td>
          <td>Ben, Cara</td>
          <td>1</td>
          <td>0</td>
          <td>0%</td>
        </tr>
        <tr>
          <td>Project Gamma</td>
          <td>Alice, Cara</td>
          <td>2</td>
          <td>1</td>
          <td>50%</td>
        </tr>
      </tbody>
    </table>

    <!-- Upcoming Tasks -->
    <div id="manager-tasks" class="mb-4">
      <h5>Upcoming Tasks (Next Due First)</h5>
      <ul class="list-group">

        <li class="list-group-item d-flex justify-content-between align-items-center bg-light border-danger">
          <div>
            <strong>Submit weekly report</strong> 
            <small class="text-muted">(Project Alpha)</small><br>
            <small class="text-danger">Importance: High</small> |
            <small>Due: 29/10/2025 - Overdue</small>
          </div>
          <small>Assigned to: Alice</small>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center bg-light border-danger">
          <div>
            <strong>Review client feedback</strong> 
            <small class="text-muted">(Project Gamma)</small><br>
            <small class="text-danger">Importance: High</small> |
            <small>Due: 31/10/2025 - Overdue</small>
          </div>
          <small>Assigned to: Cara</small>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>Attend team meeting</strong> 
            <small class="text-muted">(Project Beta)</small><br>
            <small class="text-warning">Importance: Medium</small> |
            <small>Due: 03/11/2025</small>
          </div>
          <small>Assigned to: Ben</small>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>Design mockups</strong> 
            <small class="text-muted">(Project Gamma)</small><br>
            <small class="text-warning">Importance: Medium</small> |
            <small>Due: 07/11/2025</small>
          </div>
          <small>Assigned to: Cara</small>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>Update client records</strong> 
            <small class="text-muted">(Project Alpha)</small><br>
            <small class="text-success">Importance: Low</small> |
            <small>Due: 10/11/2025</small>
          </div>
          <small>Assigned to: Alice</small>
        </li>

      </ul>
    </div>

    <!-- Chart -->
    <div id="manager-chart" class="mb-4">
      <h5>Project Completion Overview</h5>
      <canvas id="managerChart"></canvas>
    </div>

  </div>

  <!-- Footer -->
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
        labels: ["Project Alpha", "Project Beta", "Project Gamma"],
        datasets: [{
          label: "% Completion",
          data: [50, 0, 50],
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

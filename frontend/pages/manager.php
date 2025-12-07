<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Make-It-All | Manager Dashboard</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <!-- Style Sheet -->
  <link rel="stylesheet" href="/frontend/styles/style.css">

</head>
<body class="p-4 bg-light d-flex flex-column min-vh-100">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg" style="background-color: rgb(232,196,104);">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="../../assets/MakeItAllLogo.png" height="40" class="me-2"> 
        Make-It-All Ltd
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
              data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="mainNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link fw-semibold" href="#">Login</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold" href="#">Register</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold" href="#">Dashboards</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container my-4">
    <h1 id="dashboard-dash" class="mb-4">Manager Dashboard</h1>

    <!-- Summary Row -->
    <div class="row dashboard-projects mb-4">
      <div class="col-md-4 mb-3">
        <div class="dashboard-project-box blue p-3 rounded shadow-sm">
          <div class="dashboard-project-content text-center">
            <h2>Total Employees</h2>
            <h3 id="blue">3</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="dashboard-project-box green p-3 rounded shadow-sm">
          <div class="dashboard-project-content text-center">
            <h2>Total Projects</h2>
            <h3 id="green">3</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="dashboard-project-box red p-3 rounded shadow-sm">
          <div class="dashboard-project-content text-center">
            <h2>Total Tasks</h2>
            <h3 id="red">5</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Project Summary -->
    <h5 id="manager-table-title" class="mb-3">Project Summary</h5>
    <div class="table-responsive mb-4">
      <table id="manager-table" class="table table-bordered table-striped">
        <thead class="table-light">
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
    </div>

    <!-- Upcoming Tasks -->
    <div id="manager-tasks" class="mb-4">
      <h5>Upcoming Tasks (Next Due First)</h5>
      <ul class="list-group">

        <li class="list-group-item d-flex justify-content-between align-items-center bg-light border border-danger rounded mb-2">
          <div>
            <strong>Submit weekly report</strong> 
            <small class="text-muted">(Project Alpha)</small><br>
            <small class="text-danger">Importance: High</small> |
            <small>Due: 29/10/2025 - Overdue</small>
          </div>
          <small>Assigned to: Alice</small>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center bg-light border border-danger rounded mb-2">
          <div>
            <strong>Review client feedback</strong> 
            <small class="text-muted">(Project Gamma)</small><br>
            <small class="text-danger">Importance: High</small> |
            <small>Due: 31/10/2025 - Overdue</small>
          </div>
          <small>Assigned to: Cara</small>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
          <div>
            <strong>Attend team meeting</strong> 
            <small class="text-muted">(Project Beta)</small><br>
            <small class="text-warning">Importance: Medium</small> |
            <small>Due: 03/11/2025</small>
          </div>
          <small>Assigned to: Ben</small>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
          <div>
            <strong>Design mockups</strong> 
            <small class="text-muted">(Project Gamma)</small><br>
            <small class="text-warning">Importance: Medium</small> |
            <small>Due: 07/11/2025</small>
          </div>
          <small>Assigned to: Cara</small>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
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
  <footer class="footer text-center mt-auto py-3 bg-light">
    <div class="container">
      <p class="mb-0">Make-It-All Ltd | Team 27 Project</p>
    </div>
  </footer>

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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

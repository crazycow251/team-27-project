// ---------------- SHARED DATA ----------------
const sharedData = {
  employees: [
    { id: 1, name: "Alice" },
    { id: 2, name: "Ben" },
    { id: 3, name: "Cara" },
  ],
  projects: [
    { name: "Project Alpha", members: [1, 2] },
    { name: "Project Beta", members: [2, 3] },
    { name: "Project Gamma", members: [1, 3] },
  ],
  tasks: [
    { id: 1, text: "Submit weekly report", completed: true, project: "Project Alpha", employeeId: 1, importance: "High", dueDate: "2025-10-29" },
    { id: 2, text: "Attend team meeting", completed: false, project: "Project Beta", employeeId: 2, importance: "Medium", dueDate: "2025-11-03" },
    { id: 3, text: "Update client records", completed: false, project: "Project Alpha", employeeId: 1, importance: "Low", dueDate: "2025-11-10" },
    { id: 4, text: "Review client feedback", completed: true, project: "Project Gamma", employeeId: 3, importance: "High", dueDate: "2025-10-31" },
    { id: 5, text: "Design mockups", completed: false, project: "Project Gamma", employeeId: 3, importance: "Medium", dueDate: "2025-11-07" },
  ]
};

// ---------------- MANAGER DASHBOARD ----------------
class ManagerDashboard {
  constructor(root) {
    this.root = root;
  }

  init() {
    const projects = sharedData.projects.map(p => {
      const projTasks = sharedData.tasks.filter(t => t.project === p.name);
      const completed = projTasks.filter(t => t.completed).length;
      const completionRate = projTasks.length ? Math.round((completed / projTasks.length) * 100) : 0;

      return {
        name: p.name,
        members: p.members.map(id => sharedData.employees.find(e => e.id === id).name).join(", "),
        totalTasks: projTasks.length,
        completed,
        completionRate,
        upcoming: projTasks.sort((a, b) => new Date(a.dueDate) - new Date(b.dueDate)).slice(0, 3)
      };
    });

    this.root.innerHTML = `
      <h1 class="text-center mb-4">Manager Dashboard</h1>

      <div class="row mb-4 text-center">
        <div class="col"><h5>Total Employees</h5><p>${sharedData.employees.length}</p></div>
        <div class="col"><h5>Total Projects</h5><p>${sharedData.projects.length}</p></div>
        <div class="col"><h5>Total Tasks</h5><p>${sharedData.tasks.length}</p></div>
      </div>

      <h5 class="mb-3">Project Summary</h5>
      <table class="table table-bordered table-striped mb-4">
        <thead><tr><th>Project</th><th>Team Members</th><th>Tasks</th><th>Completed</th><th>Completion %</th></tr></thead>
        <tbody>
          ${projects.map(p => `
            <tr>
              <td>${p.name}</td>
              <td>${p.members}</td>
              <td>${p.totalTasks}</td>
              <td>${p.completed}</td>
              <td>${p.completionRate}%</td>
            </tr>`).join("")}
        </tbody>
      </table>

      <div class="mb-4">
        <h5>Upcoming Tasks (Next Due First)</h5>
        <ul class="list-group">
          ${sharedData.tasks
            .sort((a, b) => new Date(a.dueDate) - new Date(b.dueDate))
            .map(t => this.renderTask(t))
            .join("")}
        </ul>
      </div>

      <div class="mb-4">
        <h5>Project Completion Overview</h5>
        <canvas id="managerChart"></canvas>
      </div>
    `;

    this.renderChart(projects);
  }

  renderTask(t) {
  const today = new Date();
  const due = new Date(t.dueDate);
  const overdue = !t.completed && due < today;

  const color =
    overdue ? "text-danger fw-bold" :
    t.importance === "High" ? "text-danger" :
    t.importance === "Medium" ? "text-warning" : "text-success";

  const overdueLabel = overdue ? " ⚠️ Overdue" : "";

  return `
    <li class="list-group-item d-flex justify-content-between align-items-center ${overdue ? 'bg-light border-danger' : ''}">
      <div>
        <strong>${t.text}</strong> <small class="text-muted">(${t.project})</small><br>
        <small class="${color}">Importance: ${t.importance}</small> |
        <small>Due: ${due.toLocaleDateString()}${overdueLabel}</small>
      </div>
      <small>Assigned to: ${sharedData.employees.find(e => e.id === t.employeeId)?.name}</small>
    </li>`;
}


  renderChart(projects) {
    new Chart(this.root.querySelector("#managerChart"), {
      type: "bar",
      data: {
        labels: projects.map(p => p.name),
        datasets: [{ label: "% Completion", data: projects.map(p => p.completionRate), backgroundColor: "#198754" }]
      },
      options: { scales: { y: { beginAtZero: true, max: 100 } } }
    });
  }
}

// ---------------- AUTO INITIALIZER ----------------
document.addEventListener("DOMContentLoaded", () => {
  const empRoot = document.getElementById("employee-root");
  const mgrRoot = document.getElementById("root");

  if (empRoot) {
    new EmployeeDashboard(empRoot).init();
  } else if (mgrRoot) {
    new ManagerDashboard(mgrRoot).init();
  }
});

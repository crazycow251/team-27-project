// mainDashboard.js

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

// ---------------- EMPLOYEE DASHBOARD ----------------
class EmployeeDashboard {
  constructor(root) {
    this.root = root;
    this.employeeId = 1; // assume logged-in employee (e.g., Alice)
    this.tasks = sharedData.tasks.filter(t => t.employeeId === this.employeeId);
  }

  init() {
    // Sort tasks by due date
    this.tasks.sort((a, b) => new Date(a.dueDate) - new Date(b.dueDate));

    // Get upcoming (next 30 days)
    const today = new Date();
    const nextMonth = new Date();
    nextMonth.setDate(today.getDate() + 30);
    const upcoming = this.tasks.filter(t => new Date(t.dueDate) >= today && new Date(t.dueDate) <= nextMonth);

    this.root.innerHTML = `
      <h1 class="text-center mb-4">Employee Dashboard</h1>
      <div class="row text-center mb-4">
        <div class="col"><h5>My Tasks</h5><p>${this.tasks.length}</p></div>
        <div class="col"><h5>Completed</h5><p>${this.completedCount()}</p></div>
        <div class="col"><h5>Progress</h5><p>${this.progressPercent()}%</p></div>
      </div>

      <div class="progress mb-4" style="height: 25px;">
        <div class="progress-bar bg-success" style="width:${this.progressPercent()}%">${this.progressPercent()}%</div>
      </div>

      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title">Add a New Task</h5>
          <form id="add-task-form" class="row g-2">
            <div class="col-md-3">
              <input type="text" id="new-task-text" class="form-control" placeholder="Task description" required>
            </div>
            <div class="col-md-3">
              <input type="text" id="new-task-project" class="form-control" placeholder="Project name" required>
            </div>
            <div class="col-md-3">
              <input type="date" id="new-task-date" class="form-control" required>
            </div>
            <div class="col-md-2">
              <select id="new-task-importance" class="form-select" required>
                <option value="High">High</option>
                <option value="Medium" selected>Medium</option>
                <option value="Low">Low</option>
              </select>
            </div>
            <div class="col-md-1">
              <button class="btn btn-primary w-100" type="submit">+</button>
            </div>
          </form>
        </div>
      </div>

      <div class="mb-4">
        <h5>Your Tasks (by Due Date)</h5>
        <ul class="list-group">
          ${this.tasks.map(t => this.renderTask(t)).join("")}
        </ul>
      </div>

      <div class="mb-4">
        <h5>Upcoming (Next 30 Days)</h5>
        ${
          upcoming.length
            ? `<ul class="list-group">${upcoming.map(t => this.renderTask(t)).join("")}</ul>`
            : `<p class="text-muted">No tasks due soon.</p>`
        }
      </div>

      <div class="mb-4">
        <h5>Project Overview</h5>
        <canvas id="projectChart"></canvas>
      </div>
    `;

    this.renderChart();
    this.bindEvents();
  }

  renderTask(task) {
  const today = new Date();
  const due = new Date(task.dueDate);
  const overdue = !task.completed && due < today;

  const color =
    overdue ? "text-danger fw-bold" :
    task.importance === "High" ? "text-danger" :
    task.importance === "Medium" ? "text-warning" : "text-success";

  const overdueLabel = overdue ? " ⚠️ Overdue" : "";

  return `
    <li class="list-group-item d-flex justify-content-between align-items-center ${overdue ? 'bg-light border-danger' : ''}">
      <div>
        <strong>${task.text}</strong> <small class="text-muted">(${task.project})</small><br>
        <small class="${color}">Importance: ${task.importance}</small> |
        <small>Due: ${due.toLocaleDateString()}${overdueLabel}</small>
      </div>
      <div class="d-flex align-items-center">
        <input type="checkbox" class="form-check-input me-2" ${task.completed ? "checked" : ""} data-id="${task.id}">
        <button class="btn btn-sm btn-outline-danger delete-task" data-id="${task.id}">✕</button>
      </div>
    </li>`;
}


  completedCount() {
    return this.tasks.filter(t => t.completed).length;
  }

  progressPercent() {
    return this.tasks.length ? Math.round((this.completedCount() / this.tasks.length) * 100) : 0;
  }

  bindEvents() {
    this.root.querySelectorAll('input[type="checkbox"]').forEach(cb => {
      cb.addEventListener('change', e => {
        const id = parseInt(e.target.dataset.id);
        const task = sharedData.tasks.find(t => t.id === id);
        if (task) task.completed = e.target.checked;
        this.init();
      });
    });

    this.root.querySelectorAll('.delete-task').forEach(btn => {
      btn.addEventListener('click', e => {
        const id = parseInt(e.target.dataset.id);
        sharedData.tasks = sharedData.tasks.filter(t => t.id !== id);
        this.tasks = sharedData.tasks.filter(t => t.employeeId === this.employeeId);
        this.init();
      });
    });

    this.root.querySelector('#add-task-form').addEventListener('submit', e => {
      e.preventDefault();
      const text = this.root.querySelector('#new-task-text').value.trim();
      const project = this.root.querySelector('#new-task-project').value.trim();
      const dueDate = this.root.querySelector('#new-task-date').value;
      const importance = this.root.querySelector('#new-task-importance').value;
      if (!text || !project || !dueDate) return;

      sharedData.tasks.push({
        id: Date.now(),
        text,
        project,
        completed: false,
        employeeId: this.employeeId,
        importance,
        dueDate
      });
      this.tasks = sharedData.tasks.filter(t => t.employeeId === this.employeeId);
      this.init();
    });
  }

  renderChart() {
    const projects = [...new Set(this.tasks.map(t => t.project))];
    const counts = projects.map(p => this.tasks.filter(t => t.project === p).length);
    new Chart(this.root.querySelector("#projectChart"), {
      type: "bar",
      data: {
        labels: projects,
        datasets: [{ label: "Tasks per Project", data: counts, backgroundColor: "#0d6efd" }]
      },
      options: { scales: { y: { beginAtZero: true } } }
    });
  }
}

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

// EmployeeDashboard.js
export class EmployeeDashboard {
  constructor(root) {
    this.root = root;

    // Demo data with importance and due dates
    this.tasks = [
      { id: 1, text: "Submit weekly report", completed: true, project: "Project Alpha", importance: "High", dueDate: "2025-10-29" },
      { id: 2, text: "Attend team meeting", completed: false, project: "Project Beta", importance: "Medium", dueDate: "2025-11-05" },
      { id: 3, text: "Update client records", completed: false, project: "Project Alpha", importance: "Low", dueDate: "2025-11-20" },
    ];
  }

  init() {
    // Sort tasks by due date (soonest first)
    this.tasks.sort((a, b) => new Date(a.dueDate) - new Date(b.dueDate));

    // Filter tasks due within 30 days
    const today = new Date();
    const nextMonth = new Date();
    nextMonth.setDate(today.getDate() + 30);
    const upcomingTasks = this.tasks.filter(
      t => new Date(t.dueDate) >= today && new Date(t.dueDate) <= nextMonth
    );

    this.root.innerHTML = `
      <h1 class="text-center mb-4">Employee Dashboard</h1>
      
      <div class="row text-center mb-4">
        <div class="col"><h5>My Tasks</h5><p>${this.tasks.length}</p></div>
        <div class="col"><h5>Completed</h5><p>${this.completedCount()}</p></div>
        <div class="col"><h5>Progress</h5><p>${this.progressPercent()}%</p></div>
      </div>

      <div class="mb-4">
        <h5>Overall Progress</h5>
        <div class="progress" style="height: 25px;">
          <div class="progress-bar bg-success" role="progressbar"
            style="width: ${this.progressPercent()}%;" 
            aria-valuenow="${this.progressPercent()}" 
            aria-valuemin="0" 
            aria-valuemax="100">
            ${this.progressPercent()}%
          </div>
        </div>
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
              <select id="new-task-importance" class="form-select">
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
        <h5>Your Tasks</h5>
        <ul class="list-group" id="task-list">
          ${this.tasks.map(task => this.renderTask(task)).join('')}
        </ul>
      </div>

      <div class="mb-4">
        <h5>Upcoming (Next 30 Days)</h5>
        ${
          upcomingTasks.length
            ? `<ul class="list-group">${upcomingTasks
                .map(t => this.renderTask(t))
                .join('')}</ul>`
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
    let importanceColor =
      task.importance === "High"
        ? "text-danger"
        : task.importance === "Medium"
        ? "text-warning"
        : "text-success";

    const due = new Date(task.dueDate).toLocaleDateString();

    return `
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <strong>${task.text}</strong>
          <small class="text-muted">(${task.project})</small><br>
          <small class="${importanceColor}">Importance: ${task.importance}</small> |
          <small>Due: ${due}</small>
        </div>
        <div class="d-flex align-items-center">
          <input type="checkbox" class="form-check-input me-2" ${task.completed ? "checked" : ""} data-id="${task.id}">
          <button class="btn btn-sm btn-outline-danger delete-task" data-id="${task.id}">✕</button>
        </div>
      </li>
    `;
  }

  completedCount() {
    return this.tasks.filter(t => t.completed).length;
  }

  progressPercent() {
    return this.tasks.length
      ? Math.round((this.completedCount() / this.tasks.length) * 100)
      : 0;
  }

  bindEvents() {
    const checkboxes = this.root.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => {
      cb.addEventListener('change', e => {
        const id = parseInt(e.target.dataset.id);
        const task = this.tasks.find(t => t.id === id);
        if (task) task.completed = e.target.checked;
        this.init();
      });
    });

    const deleteButtons = this.root.querySelectorAll('.delete-task');
    deleteButtons.forEach(btn => {
      btn.addEventListener('click', e => {
        const id = parseInt(e.target.dataset.id);
        this.tasks = this.tasks.filter(t => t.id !== id);
        this.init();
      });
    });

    const form = this.root.querySelector('#add-task-form');
    form.addEventListener('submit', e => {
      e.preventDefault();
      const text = this.root.querySelector('#new-task-text').value.trim();
      const project = this.root.querySelector('#new-task-project').value.trim();
      const dueDate = this.root.querySelector('#new-task-date').value;
      const importance = this.root.querySelector('#new-task-importance').value;

      if (!text || !project || !dueDate) return;

      this.tasks.push({
        id: Date.now(),
        text,
        project,
        completed: false,
        importance,
        dueDate
      });
      this.init();
    });
  }

  renderChart() {
    const projectNames = [...new Set(this.tasks.map(t => t.project))];
    const taskCounts = projectNames.map(name => this.tasks.filter(t => t.project === name).length);

    const ctx = this.root.querySelector('#projectChart');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: projectNames,
        datasets: [{
          label: 'Tasks per Project',
          data: taskCounts,
          backgroundColor: '#0d6efd'
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } }
      }
    });
  }
}

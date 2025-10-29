//Dashboard.js
export class ManagerDashboard {
  constructor(root) {
    this.root = root;
  }

  init() {
    this.root.innerHTML = `
      <h1 class="text-center mb-4">Manager Dashboard</h1>
      <div class="row text-center mb-4">
        <div class="col"><h5>Total Projects</h5><p>3</p></div>
        <div class="col"><h5>Overdue Tasks</h5><p>2</p></div>
        <div class="col"><h5>Completion Rate</h5><p>68%</p></div>
      </div>

      <div class="mb-4">
        <h5>Project Progress</h5>
        <canvas id="progressChart"></canvas>
      </div>

      <div class="mb-4">
        <h5>Team Workload</h5>
        <canvas id="workloadChart"></canvas>
      </div>
    `;

    this.renderCharts();
  }

  renderCharts() {
    new Chart(document.getElementById('progressChart'), {
      type: 'bar',
      data: {
        labels: ['Alpha', 'Beta', 'Gamma'],
        datasets: [
          {
            label: '% Complete',
            data: [80, 60, 65],
            backgroundColor: ['#0d6efd', '#198754', '#ffc107']
          }
        ]
      },
      options: {
        scales: { y: { beginAtZero: true, max: 100 } }
      }
    });

    new Chart(document.getElementById('workloadChart'), {
      type: 'bar',
      data: {
        labels: ['Alice', 'Bob', 'Charlie', 'Diana'],
        datasets: [
          { label: 'Assigned', data: [5, 8, 2, 7], backgroundColor: '#0d6efd' },
          { label: 'Completed', data: [3, 6, 2, 5], backgroundColor: '#198754' }
        ]
      },
      options: { scales: { y: { beginAtZero: true } } }
    });
  }
}

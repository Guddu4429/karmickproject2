document.addEventListener("DOMContentLoaded", function () {

  const ctx = document.getElementById("attendanceChart");

  if (!ctx) return;

  new Chart(ctx, {
    type: "line",
    data: {
      labels: ["Aug", "Sep", "Oct", "Nov", "Dec", "Jan"],
      datasets: [
        {
          label: "Attendance %",
          data: [92, 88, 85, 90, 94, 89],
          borderWidth: 2,
          tension: 0.4,
          fill: true,
          backgroundColor: "rgba(13, 110, 253, 0.1)",   // bootstrap primary
          borderColor: "rgba(13, 110, 253, 1)",
          pointBackgroundColor: "rgba(13, 110, 253, 1)",
          pointRadius: 4
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          min: 0,
          max: 100,
          ticks: {
            callback: function (value) {
              return value + "%";
            }
          }
        }
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              return context.parsed.y + "% attendance";
            }
          }
        }
      }
    }
  });

});

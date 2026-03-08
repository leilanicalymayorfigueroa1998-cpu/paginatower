/**
 * DASHBOARD — dashboard_mod.js
 *
 * Variables inyectadas desde view.php:
 *   DASH_CHART_LABELS   → string[]   (ej. ['Jan','Feb',...])
 *   DASH_CHART_INGRESOS → number[]
 *   DASH_CHART_EGRESOS  → number[]
 */

/* ── Defaults globales de Chart.js ─────────── */
Chart.defaults.color        = '#8494b0';
Chart.defaults.borderColor  = 'rgba(255,255,255,0.06)';
Chart.defaults.font.family  = "'Epilogue', sans-serif";
Chart.defaults.font.size    = 12;

/* ── Gráfica de barras 6 meses ──────────────── */
(function () {
  const canvas = document.getElementById('graficaMeses');
  if (!canvas) return;

  new Chart(canvas, {
    type: 'bar',
    data: {
      labels: DASH_CHART_LABELS,
      datasets: [
        {
          label: 'Ingresos',
          data: DASH_CHART_INGRESOS,
          backgroundColor: 'rgba(16,185,129,0.7)',
          borderRadius: 6,
          borderSkipped: false,
        },
        {
          label: 'Egresos',
          data: DASH_CHART_EGRESOS,
          backgroundColor: 'rgba(244,63,94,0.6)',
          borderRadius: 6,
          borderSkipped: false,
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
          labels: { color: '#8494b0', boxWidth: 10, padding: 16, font: { size: 12 } }
        },
        tooltip: {
          backgroundColor: '#141c2e',
          borderColor: 'rgba(255,255,255,0.11)',
          borderWidth: 1,
          titleColor: '#dde4f0',
          bodyColor: '#8494b0',
          callbacks: {
            label: ctx => ' $' + ctx.parsed.y.toLocaleString('es-MX')
          }
        }
      },
      scales: {
        x: {
          grid:  { color: 'rgba(255,255,255,0.04)', drawBorder: false },
          ticks: { color: '#8494b0' }
        },
        y: {
          grid:  { color: 'rgba(255,255,255,0.04)', drawBorder: false },
          ticks: {
            color: '#8494b0',
            callback: v => '$' + (v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v)
          }
        }
      }
    }
  });
})();

@once
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    window.SocietyCharts = {
        instances: {},
        palette: ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#64748b'],
        render(configs) {
            if (typeof Chart === 'undefined') return;
            configs.forEach(({ id, type, data, options }) => {
                const canvas = document.getElementById(id);
                if (!canvas) return;
                if (this.instances[id]) {
                    this.instances[id].destroy();
                }
                this.instances[id] = new Chart(canvas, {
                    type: type || 'bar',
                    data: data,
                    options: Object.assign({
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                        },
                    }, options || {}),
                });
            });
        },
        boot() {
            document.querySelectorAll('[data-chart-config]').forEach(el => {
                try {
                    const configs = JSON.parse(el.textContent);
                    this.render(configs);
                } catch (e) { console.warn('Chart config error', e); }
            });
        },
    };
    const bootCharts = () => setTimeout(() => window.SocietyCharts.boot(), 80);
    document.addEventListener('DOMContentLoaded', bootCharts);
    document.addEventListener('livewire:navigated', bootCharts);
    document.addEventListener('livewire:init', () => {
        Livewire.hook('morph.updated', ({ el }) => {
            if (el.querySelector?.('[data-chart-config]') || el.matches?.('[data-chart-config]')) {
                bootCharts();
            }
        });
    });
</script>
@endonce

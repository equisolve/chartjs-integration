// Setup config data for the charts
function prep_config(cd) {
    let pcd = {};
    pcd.type = cd.type;
    
    // Add in the chart data
    pcd.data = {
        labels: cd.labels,
        datasets: cd.datasets
    };
    pcd.labels = cd.labels;
    
    // Set the basic options
    pcd.options = {
        spanGaps: true,
        maintainAspectRatio: false,
        plugins: {},
    };
    
    // Legend plugin
    // (for pie and doughnut charts, the legends are set in the overrides below)
    pcd.options.plugins.legend = {
        position: cd.legend_pos,
        align: cd.legend_alignment
    }
    
    // Annotation Plugin
    if (cd.overlays.length > 0 && (cd.type == 'bar' || cd.type == 'line')) {
        let overlays = {};
        cd.overlays.forEach((el, ind) => {
            overlays['annotation-' + ind] = {
                type: 'line',
                yMin: parseInt(el.y1),
                xMin: el.x1,
                yMax: parseInt(el.y2),
                xMax: el.x2,
                borderColor: el.color,
                borderWidth: 3
            };
            if (el.label) {
                overlays['annotation-' + ind].label = {
                    rotation: 'auto',
                    backgroundColor: el.color,
                    content: el.label,
                    enabled: true
                }
            }
        });
        pcd.options.plugins.annotation = {
            drawTime: 'afterDatasetsDraw',
            annotations: overlays
        };
    }
    pcd.options.scales = {
        y: {},
        x: {
            grid: {
                display: false,
            }
        }
    };
    
    // Data Labels Plugin
    pcd.options.plugins.datalabels = {
        color: 'white',
        display: (context) => {
            return cd.datasets[context.datasetIndex].show_dataset_labels;
        },
        font: {
            weight: 'bold'
        },
        formatter: Math.round
    };
    
    // Add units to the specified axes
    if ((cd.type == 'line' || cd.type == 'bar') && (cd.y1_prefix || cd.y1_suffix)) {
        pcd.options.scales.y.ticks = {
            callback : (value, index, values) => {
                // Add commas to big numbers
                let num = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return cd.y1_prefix + num + cd.y1_suffix;
            }
        };
    }
    
    // Setup stacked bars
    pcd.options.scales.x.stacked = cd.stacked == 1;
    pcd.options.scales.y.stacked = cd.stacked == 1;
    
    // Setup double axis
    if (cd.second_axis) {
        pcd.options.scales.y2 = {
            type: 'linear',
            display: true,
            position: 'right',
            grid: {
                drawOnChartArea: false
            }
        }
        if (cd.y2_prefix || cd.y2_suffix) {
            pcd.options.scales.y2.ticks = {
                callback : (value, index, values) => { return cd.y2_prefix + value + cd.y2_suffix; }
            }
        }
    }
    
    // Overrides
    pcd.overrides = [];
    if (cd.type == 'pie' || cd.type == 'doughnut') {
        pcd.options.scales.x.display = false;
        pcd.options.scales.y.display = false;
        pcd.overrides[cd.type] = {
            plugins: {
                legend: {
                    position: cd.legend_pos,
                    align: cd.legend_alignment,
                }
            }
        }
    }
    return pcd;
}

document.addEventListener("DOMContentLoaded", (event) => {
    let chart_groups = [];
    document.querySelectorAll('.module-charts-v2').forEach((chart_group) => {
        let data = JSON.parse(document.getElementById("charts-data-" + chart_group.dataset.row).innerText);
        chart_groups.push(data);
    });
    document.querySelectorAll('.sub-tabs').forEach((chart_group) => {
        let data = JSON.parse(document.getElementById("charts-data-subtable-" + chart_group.dataset.row).innerText);
        chart_groups.push(data);
    });
    // Register plugin
    Chart.register(ChartDataLabels);
    
    chart_groups.forEach((charts_data) => {
        charts_data.all_data.forEach((cd) => {
            let el = document.getElementById('chart-' + cd.id);
            let sheet_el = document.getElementById('chart-spreadsheet-' + cd.id);
            if (el) {
                if (cd.type) {
                    new Chart(el, prep_config(cd));
                } else {
                    console.error('Chart ID: ' + cd.id + ' does not have a chart type selected');
                }
            }
            if (sheet_el) {
                let sheet = jspreadsheet(sheet_el, {
                    data: cd.sheet_data,
                    columns: [{}],
                    config: {
                        columnResize: true
                    }
                });
            }
        });
    });
});
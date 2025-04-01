<template>
    <Bar :chart-options="chartOptions" :chart-data="chartData" :chart-id="chartId" :dataset-id-key="datasetIdKey"
        :plugins="plugins" :css-classes="cssClasses" :styles="styles" />
</template>

<script>
import { Bar } from 'vue-chartjs/legacy'

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
    name: 'BarChart',
    components: {
        Bar
    },
    props: {
        chartId: {
            type: String,
            default: 'bar-chart'
        },
        datasetIdKey: {
            type: String,
            default: 'label'
        },
        cssClasses: {
            default: '',
            type: String
        },
        styles: {
            type: Object,
            default: () => { }
        },
        plugins: {
            type: Array,
            default: () => []
        },
    },
    data() {
        return {
            chartData: {
                labels: [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ],
                datasets: [
                    {
                        label: 'Income (ZAR)',
                        type: 'bar',
                        backgroundColor: 'lightgreen',
                        data: [1363504.80, 717943.88, 1897420.88, 989087.41, 1953469.70, 1023653.66, 2776678.24, 1519445.41, 3191733.61, 2955737.61, 813889.15, 190301.78]
                    },
                    {
                        label: 'Expenses (ZAR)',
                        type: 'bar',
                        backgroundColor: '#FF7F7F',
                        data: [288114.87, 650251.05, 473109.37, 584411.84, 768362.31, 304372.85, 466683.09, 503031.92, 654671.48, 427382.5, 809700.51, 637432.05]
                    },
                    {
                        label: 'Total Profit (ZAR)',
                        type: 'bar',
                        backgroundColor: 'skyblue',
                        data: [1075389.93, 67692.83, 1424311.51, 404675.57, 1185107.39, 719280.81, 2309995.15, 1016413.49, 2537062.15, 2528355.11, 4188.64, -447130.27]
                    }
                ]
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
            }
        }
    }
}
</script>
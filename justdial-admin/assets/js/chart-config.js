/**
 * Chart configurations for the Justdial Admin Dashboard
 * Uses Chart.js for rendering various charts
 */

// Chart color palette
const chartColors = {
    primary: '#4f46e5',
    secondary: '#6366f1',
    success: '#10b981',
    danger: '#ef4444',
    warning: '#f59e0b',
    info: '#3b82f6',
    light: '#f3f4f6',
    dark: '#1f2937',
    primaryLight: '#c7d2fe',
    secondaryLight: '#ddd6fe',
    successLight: '#a7f3d0',
    dangerLight: '#fecaca',
    warningLight: '#fde68a',
    infoLight: '#bfdbfe',
    // Gradient colors
    primaryGradient: {
        start: '#4f46e5',
        end: '#6366f1'
    },
    successGradient: {
        start: '#10b981',
        end: '#34d399'
    },
    dangerGradient: {
        start: '#ef4444',
        end: '#f87171'
    }
};

// Common chart options
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
            labels: {
                usePointStyle: true,
                padding: 20,
                font: {
                    size: 12,
                    family: "'Inter', sans-serif"
                }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(17, 24, 39, 0.8)',
            titleFont: {
                size: 13,
                family: "'Inter', sans-serif",
                weight: '600'
            },
            bodyFont: {
                size: 12,
                family: "'Inter', sans-serif"
            },
            padding: 12,
            cornerRadius: 8,
            displayColors: true
        }
    }
};

/**
 * Initialize all dashboard charts
 */
function initDashboardCharts() {
    // Initialize charts if Chart.js is available
    if (typeof Chart === 'undefined') return;
    
    // Set default font for all charts
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6b7280';
    
    // Initialize each chart
    initRevenueChart();
    initUsersChart();
    initBusinessesChart();
    initReviewsChart();
    initCategoryDistributionChart();
    initUserActivityChart();
}

/**
 * Create a gradient for chart backgrounds
 */
function createGradient(ctx, startColor, endColor) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, startColor);
    gradient.addColorStop(1, endColor);
    return gradient;
}

/**
 * Initialize revenue chart
 */
function initRevenueChart() {
    const revenueChart = document.getElementById('revenue-chart');
    if (!revenueChart) return;
    
    const ctx = revenueChart.getContext('2d');
    const gradient = createGradient(ctx, 'rgba(79, 70, 229, 0.2)', 'rgba(79, 70, 229, 0)');
    
    // Sample data - replace with actual data from your backend
    const data = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Revenue',
            data: [18500, 21000, 24000, 22500, 28000, 26000, 32000, 35000, 33000, 38000, 36000, 40000],
            borderColor: chartColors.primary,
            backgroundColor: gradient,
            borderWidth: 2,
            tension: 0.3,
            fill: true,
            pointBackgroundColor: chartColors.primary,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    };
    
    const options = {
        ...commonOptions,
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    borderDash: [2, 2]
                },
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            ...commonOptions.plugins,
            tooltip: {
                ...commonOptions.plugins.tooltip,
                callbacks: {
                    label: function(context) {
                        return 'Revenue: $' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    };
    
    new Chart(ctx, {
        type: 'line',
        data: data,
        options: options
    });
}

/**
 * Initialize users chart
 */
function initUsersChart() {
    const usersChart = document.getElementById('users-chart');
    if (!usersChart) return;
    
    const ctx = usersChart.getContext('2d');
    
    // Sample data - replace with actual data from your backend
    const data = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'New Users',
            data: [120, 150, 180, 170, 210, 230],
            backgroundColor: chartColors.primaryLight,
            borderColor: chartColors.primary,
            borderWidth: 2,
            borderRadius: 4,
            barThickness: 12
        }, {
            label: 'Active Users',
            data: [320, 350, 380, 370, 410, 430],
            backgroundColor: chartColors.infoLight,
            borderColor: chartColors.info,
            borderWidth: 2,
            borderRadius: 4,
            barThickness: 12
        }]
    };
    
    const options = {
        ...commonOptions,
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    borderDash: [2, 2]
                }
            }
        }
    };
    
    new Chart(ctx, {
        type: 'bar',
        data: data,
        options: options
    });
}

/**
 * Initialize businesses chart
 */
function initBusinessesChart() {
    const businessesChart = document.getElementById('businesses-chart');
    if (!businessesChart) return;
    
    const ctx = businessesChart.getContext('2d');
    const gradient = createGradient(ctx, 'rgba(16, 185, 129, 0.2)', 'rgba(16, 185, 129, 0)');
    
    // Sample data - replace with actual data from your backend
    const data = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'New Businesses',
            data: [25, 32, 28, 35, 40, 38, 45, 48, 50, 55, 58, 65],
            borderColor: chartColors.success,
            backgroundColor: gradient,
            borderWidth: 2,
            tension: 0.3,
            fill: true,
            pointBackgroundColor: chartColors.success,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    };
    
    const options = {
        ...commonOptions,
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    borderDash: [2, 2]
                }
            }
        }
    };
    
    new Chart(ctx, {
        type: 'line',
        data: data,
        options: options
    });
}

/**
 * Initialize reviews chart
 */
function initReviewsChart() {
    const reviewsChart = document.getElementById('reviews-chart');
    if (!reviewsChart) return;
    
    const ctx = reviewsChart.getContext('2d');
    
    // Sample data - replace with actual data from your backend
    const data = {
        labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
        datasets: [{
            label: 'Reviews',
            data: [350, 280, 120, 50, 30],
            backgroundColor: [
                chartColors.success,
                chartColors.info,
                chartColors.warning,
                chartColors.secondary,
                chartColors.danger
            ],
            borderWidth: 0,
            hoverOffset: 10
        }]
    };
    
    const options = {
        ...commonOptions,
        cutout: '60%',
        plugins: {
            ...commonOptions.plugins,
            tooltip: {
                ...commonOptions.plugins.tooltip,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    };
    
    new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: options
    });
}

/**
 * Initialize category distribution chart
 */
function initCategoryDistributionChart() {
    const categoryChart = document.getElementById('category-chart');
    if (!categoryChart) return;
    
    const ctx = categoryChart.getContext('2d');
    
    // Sample data - replace with actual data from your backend
    const data = {
        labels: ['Restaurants', 'Hotels', 'Shopping', 'Services', 'Entertainment', 'Healthcare', 'Education', 'Others'],
        datasets: [{
            label: 'Businesses',
            data: [120, 85, 95, 75, 60, 70, 50, 40],
            backgroundColor: [
                chartColors.primary,
                chartColors.success,
                chartColors.warning,
                chartColors.info,
                chartColors.secondary,
                chartColors.danger,
                '#9333ea', // Purple
                '#64748b'  // Slate
            ],
            borderWidth: 0
        }]
    };
    
    const options = {
        ...commonOptions,
        indexAxis: 'y',
        scales: {
            x: {
                beginAtZero: true,
                grid: {
                    borderDash: [2, 2]
                }
            },
            y: {
                grid: {
                    display: false
                }
            }
        }
    };
    
    new Chart(ctx, {
        type: 'bar',
        data: data,
        options: options
    });
}

/**
 * Initialize user activity chart
 */
function initUserActivityChart() {
    const activityChart = document.getElementById('user-activity-chart');
    if (!activityChart) return;
    
    const ctx = activityChart.getContext('2d');
    
    // Sample data - replace with actual data from your backend
    const data = {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Searches',
            data: [1200, 1350, 1250, 1400, 1500, 1800, 1600],
            borderColor: chartColors.primary,
            backgroundColor: 'rgba(79, 70, 229, 0.2)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }, {
            label: 'Page Views',
            data: [2200, 2350, 2100, 2400, 2500, 2800, 2600],
            borderColor: chartColors.info,
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }]
    };
    
    const options = {
        ...commonOptions,
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    borderDash: [2, 2]
                }
            }
        }
    };
    
    new Chart(ctx, {
        type: 'line',
        data: data,
        options: options
    });
}

/**
 * Create a custom chart
 * @param {string} elementId - The ID of the canvas element
 * @param {string} type - Chart type (line, bar, pie, doughnut, etc.)
 * @param {object} data - Chart data
 * @param {object} options - Chart options
 */
function createCustomChart(elementId, type, data, options = {}) {
    const chartElement = document.getElementById(elementId);
    if (!chartElement) return;
    
    const ctx = chartElement.getContext('2d');
    
    const chartOptions = {
        ...commonOptions,
        ...options
    };
    
    return new Chart(ctx, {
        type: type,
        data: data,
        options: chartOptions
    });
}

// Export functions for use in other scripts
window.chartConfig = {
    initDashboardCharts,
    createCustomChart,
    chartColors
};
var general = document.getElementById("generalChart");
const genValues = [income, expense];
var generalChart = new Chart(general, {
    // The type of chart we want to create
    type: 'doughnut',

    // The data for our dataset
    data: {
        labels: [tincome, texpense], //tu muszą iść tagi
        datasets: [{
            label: 'General',
            backgroundColor: [ 'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
            ],
            borderColor: ['rgb(255, 120, 132)',

            ],
            data: genValues //a tu muszą iść zsumowane wartości
        }]
    },

    // Configuration options go here
    options: {
        responsive: true,
    }
});
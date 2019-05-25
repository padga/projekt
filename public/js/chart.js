const categories = categoryArray;
const values = amountArray;
// const colors = ['#a256ec', '#eb8165', '#0cb2ba'];

let  colors = [];
function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

let j;
for (j =0; j < categories.length; j++ ){
    colors.push(getRandomColor());
}
console.log(colors);

let ctx = document.getElementById("myChart");
let myChart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'doughnut',

    // The data for our dataset
    data: {
        labels: categories,
        datasets: [{
            backgroundColor: colors,
            data: values,
        }]
    },
    options: {
        responsive: true,
        // onClick: graphClickEvent,
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 25,
                bottom: 50
            }
        }
    }
});



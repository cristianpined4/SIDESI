import "./bootstrap";
import Swal from "sweetalert2";
import Chart from "chart.js/auto";
import moment from "moment";

window.moment = moment;
window.months =
    "_Enero_Febrero_Marzo_Abril_Mayo_Junio_Julio_Agosto_Septiembre_Octubre_Noviembre_Diciembre".split(
        "_"
    );

const Alert = (title, text, icon) => {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: "Ok",
    });
};

window.Alert = Alert;

const Confirm = async (
    title,
    text,
    icon,
    confirmButtonText,
    cancelButtonText
) => {
    const result = await Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
    });

    return result.isConfirmed; // Devuelve true si se confirmó, false si se canceló
};

window.Confirm = Confirm;

const hiddenLoader = () => {
    let el = document.getElementById("loader");
    if (el != null) {
        el.classList.remove("show");
    }
};

window.hiddenLoader = hiddenLoader;

const showLoader = () => {
    let el = document.getElementById("loader");
    if (el != null) {
        el.classList.add("show");
    }
};

window.showLoader = showLoader;

function generarGraficoBarras(selector, etiquetas, datos) {
    // Generar colores aleatorios
    const coloresAleatorios = datos.map(() => {
        const randomColor = () => Math.floor(Math.random() * 256);
        return `rgba(${randomColor()}, ${randomColor()}, ${randomColor()}, 1)`;
    });

    // Crear el gráfico
    const ctx = document.querySelector(selector).getContext("2d");
    const myChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: etiquetas,
            datasets: [
                {
                    label: "Total",
                    data: datos,
                    backgroundColor: coloresAleatorios.map((color) =>
                        color.replace("1)", "0.3)")
                    ),
                    borderColor: coloresAleatorios,
                    borderWidth: 1,
                },
            ],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });

    return myChart;
}

window.chartJs = generarGraficoBarras;

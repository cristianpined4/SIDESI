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

const openModal = (modal) => {
    modal.style.display = "flex";
};

window.openModal = openModal;

const closeModal = (modal) => {
    modal.style.display = "none";
    clearForm(modal);
};

window.closeModal = closeModal;

const clearForm = (modal) => {
    const inputs = modal.querySelectorAll("input, textarea, select");
    inputs.forEach((input) => {
        if (input.type === "checkbox" || input.type === "radio") {
            input.checked = false;
        } else {
            input.value = "";
        }
        hideError(input);
    });
};

window.clearForm = clearForm;

const validateInput = (input) => {
    if (input.type === "email") {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value);
    } else if (input.type === "checkbox") {
        return input.checked;
    } else if (input.type === "radio") {
        const radios = document.getElementsByName(input.name);
        return Array.from(radios).some((r) => r.checked);
    } else if (input.type === "file") {
        return input.files.length > 0;
    } else {
        return input.value.trim() !== "";
    }
};

window.validateInput = validateInput;

const showError = (input, message) => {
    input.classList.add("is-invalid");
    if (input.nextElementSibling) {
        input.nextElementSibling.innerText = message;
        input.nextElementSibling.style.display = "block";
    }
};

window.showError = showError;

const hideError = (input) => {
    input.classList.remove("is-invalid");
    if (input.nextElementSibling)
        input.nextElementSibling.style.display = "none";
};

window.hideError = hideError;

document.addEventListener("Livewire:initialized", () => {
    // Abrir modal
    const modalButtons = document.querySelectorAll("[data-modal-target]");
    modalButtons.forEach((btn) => {
        const target = document.querySelector(btn.dataset.modalTarget);
        if (!target) return;
        btn.addEventListener("click", () => openModal(target));
    });

    // Cerrar modal
    const closeButtons = document.querySelectorAll(
        ".modal .btn-close, .modal .btn-secondary"
    );

    closeButtons.forEach((btn) => {
        const modal = btn.closest(".modal");
        btn.addEventListener("click", () => closeModal(modal));
    });

    // Cerrar al hacer clic fuera
    const modals = document.querySelectorAll(".modal");
    modals.forEach((modal) => {
        modal.addEventListener("click", (e) => {
            if (e.target === modal) closeModal(modal);
        });
    });
});

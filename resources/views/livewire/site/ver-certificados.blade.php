@section('title', 'Verificar Certificado')

<main class="bg-gray-100 py-10 px-4 flex flex-col items-center">
    @if ($certificate->is_valid)
    <style>
        .diploma-container * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .diploma-container {
            width: 1000px;
            height: 707px;
            background: linear-gradient(135deg, #e8e8e8 0%, #ffffff 100%);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .diploma-container::before {
            content: "DUPLICADO";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 10rem;
            color: rgba(145, 145, 145, 0.5);
            font-weight: 900;
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
        }

        /* Decoraciones geométricas */
        .diploma-container .corner-decoration {
            position: absolute;
            width: 0;
            height: 0;
        }

        .diploma-container .top-left {
            top: 0;
            left: 0;
            border-left: 180px solid #1ba3c6;
            border-bottom: 180px solid transparent;
        }

        .diploma-container .top-left-inner {
            top: 0;
            left: 0;
            border-left: 140px solid #1e3a8a;
            border-bottom: 140px solid transparent;
        }

        .diploma-container .bottom-right {
            bottom: 0;
            right: 0;
            border-right: 180px solid #1e3a8a;
            border-top: 180px solid transparent;
        }

        .diploma-container .bottom-right-inner {
            bottom: 0;
            right: 0;
            border-right: 140px solid #1ba3c6;
            border-top: 140px solid transparent;
        }

        /* Contenido del diploma */
        .diploma-container .diploma-content {
            position: relative;
            padding: 80px 100px;
            text-align: center;
        }

        .diploma-container .title {
            color: #1e3a8a;
            font-size: 90px;
            font-weight: 900;
            letter-spacing: 8px;
            margin-bottom: 25px;
            line-height: 1;
            margin-top: -25px;
        }

        .diploma-container .subtitle {
            color: #000;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 30px;
        }

        .diploma-container .intro-text {
            color: #000;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 400;
        }

        .diploma-container .recipient-name {
            font-family: 'Brush Script MT', cursive;
            font-size: 56px;
            color: #000;
            margin: 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
            font-weight: normal;
            font-style: italic;
            white-space: nowrap;
            word-break: keep-all;
        }

        .diploma-container .description {
            color: #000;
            font-size: 14px;
            line-height: 1.6;
            margin: 20px 0;
            text-align: justify;
            font-weight: 500;
        }

        .diploma-container .university-info {
            color: #000;
            font-size: 13px;
            margin: 25px 0;
            font-weight: 500;
        }

        .diploma-container .signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 20px;
            padding: 0 50px;
        }

        .diploma-container .signature-block {
            text-align: center;
            flex: 1;
        }

        .diploma-container .signature-line {
            width: 200px;
            height: 1px;
            background-color: #000;
            margin: 0 auto 8px;
        }

        .diploma-container .signature-name {
            font-size: 13px;
            font-weight: 700;
            color: #000;
            line-height: 1.4;
        }

        .diploma-container .logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            flex: 0 0 auto;
        }

        .diploma-container .logo-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #1e3a8a;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
        }

        .diploma-container .logo-text {
            font-size: 24px;
            font-weight: 900;
            color: #1e3a8a;
            /* margin-bottom: 20px; */
        }

        .diploma-container .university-seal {
            width: 100px;
            height: 100px;
            border: 3px solid #c41e3a;
            border-radius: 50%;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .diploma-container .seal-inner {
            width: 90%;
            height: 90%;
            border: 2px solid #c41e3a;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #c41e3a;
            font-weight: 700;
            text-align: center;
            padding: 5px;
        }

        .diploma-container .seal-inner span {
            /* margin-bottom: 10px; */
        }

        .diploma-container .university-logo {
            position: absolute;
            top: 75px;
            left: 15%;
            transform: translateX(-50%);
            width: 180px;
            height: auto;
        }

        .diploma-container .university-logo img {
            width: 100%;
            height: auto;
        }

        .diploma-container .qr-code {
            position: absolute;
            top: 75px;
            right: 15%;
            transform: translateX(50%);
            width: 140px;
            height: 140px;
        }

        .diploma-container .qr-code img {
            width: 100%;
            height: 100%;
        }

        .diploma-container .qr-code-text {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translate(-50%, 10px);
            font-size: 10px;
            color: #000;
            width: 160px;
            text-align: center;
        }

        .diploma-container .qr-code-text span {
            font-weight: 700;
        }

        .diploma-container .unique_code {
            position: absolute;
            bottom: 20px;
            left: 20px;
            font-size: 10px;
            color: #555;
            font-weight: 500;
        }

        .diploma-container .unique_code span {
            font-weight: 700;
        }
    </style>

    <!-- Mensaje principal -->
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold text-green-600 mb-8">🎓 Certificado Válido</h2>
        <p class="text-gray-700 text-lg max-w-7xl mx-auto">
            El código <strong class="font-semibold text-gray-900">{{ $certificate->code }}</strong>
            corresponde a un certificado emitido oficialmente por la
            <span class="font-semibold text-blue-700">Asociación de Estudiantes de Ingeniería en Sistemas Informáticos
                (ASEIS)</span>
            y por la
            <span class="font-semibold text-blue-700">Facultad Multidisciplinaria Oriental (FMO)</span>
            de la
            <span class="font-semibold text-blue-700">Universidad de El Salvador (UES)</span>.
        </p>
    </div>

    @php
    $name_capitalized = ucwords(strtolower($certificate->recipient_name));
    @endphp

    <!-- Diploma visual -->
    <div class="w-full max-w-7xl mx-auto bg-white rounded-2xl shadow-2xl border-4 border-blue-400 p-4 
                overflow-x-auto">
        <div class="diploma-container" style="display: block;overflow: auto;margin: 0 auto;overflow-y: hidden;">
            <!-- Decoraciones de esquinas -->
            <div class="corner-decoration top-left"></div>
            <div class="corner-decoration top-left-inner"></div>
            <div class="corner-decoration bottom-right"></div>
            <div class="corner-decoration bottom-right-inner"></div>

            <!-- Logo de la universidad -->
            <div class="university-logo">
                <img src="{{ url('/images/logoues.png') }}" alt="Logo Universidad de El Salvador">
            </div>

            <!-- codigo QR -->
            <div class="qr-code">
                <img src="{{ $certificate->qr_code }}" alt="Código QR">
                <div class="qr-code-text">Escanea este código para verificar tu diploma.</div>
            </div>

            <!-- Contenido del diploma -->
            <div class="diploma-content">
                <h1 class="title">DIPLOMA</h1>
                <h2 class="subtitle">DE RECONOCIMIENTO</h2>

                <p class="intro-text">Se le otorga el presente diploma a</p>

                <div class="recipient-name">{{ $name_capitalized }}</div>

                <p class="description">
                    En reconocimiento a su valiosa participación y aporte al desarrollo de
                    <strong class="event_name">{{ $certificate->event_name }}</strong>, demostrando excelencia,
                    compromiso y
                    entusiasmo
                    en todas las
                    actividades realizadas.
                </p>

                <p class="university-info">
                    Universidad de El Salvador, Facultad Multidisciplinaria Oriental, <span class="date">{{
                        $certificate->date }}</span>
                </p>

                <div class="signatures">
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-name">
                            <!-- Karla Orellana<br> -->
                            Presidente(a) de ASEIS.
                        </div>
                    </div>

                    <div class="logos">
                        <div class="logo-circle">
                            <div class="logo-text">ASEIS</div>
                        </div>
                        <div class="university-seal">
                            <div class="seal-inner">
                                <span>
                                    UNIVERSIDAD<br>DE EL<br>SALVADOR
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-name">
                            <!-- Msc. Iván Franco<br> -->
                            Decano(a) de la UES-FMO.
                        </div>
                    </div>
                </div>
            </div>

            <div class="unique_code">Código de verificación: <span class="code">{{ $certificate->code }}</span>
                <span class="font-bold" style="margin-left: 5rem">--- Versión digital del documento original ---</span>
            </div>
        </div>
    </div>

    <p class="mt-15 mb-4 max-w-7xl mx-auto text-justify leading-relaxed text-gray-700 text-lg px-6 font-serif">
        La <span class="font-semibold text-blue-700">Asociación de Estudiantes de Ingeniería en Sistemas Informáticos
            (ASEIS)</span>,
        junto con la <span class="font-semibold text-blue-700">Facultad Multidisciplinaria Oriental (FMO)</span> de la
        <span class="font-semibold text-blue-700">Universidad de El Salvador (UES)</span>, certifican que el portador
        del
        presente diploma
        ha participado de manera destacada en el evento antes mencionado, demostrando compromiso, dedicación y
        espíritu académico durante su desarrollo.
    </p>

    <!-- Botón de descarga -->
    <div class="mt-10 mb-10">
        <button
            class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold text-lg rounded-lg shadow-md transition-all duration-200 btn-diploma"
            onclick="descargar()">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15zM12 13l3 3m0 0l3-3m-3 3V8" />
            </svg>
            Descargar PDF
        </button>
    </div>
    @else
    <!-- Mensaje de certificado inválido -->
    <div class="text-center my-12 px-6">
        <div
            class="inline-flex items-center gap-3 bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded-xl shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v2m0 4h.01M4.93 4.93a10 10 0 1114.14 14.14A10 10 0 014.93 4.93z" />
            </svg>
            <h2 class="text-2xl font-bold text-red-700 tracking-tight">Certificado Inválido</h2>
        </div>

        <p class="mt-6 max-w-3xl mx-auto text-gray-700 text-lg leading-relaxed">
            El código <strong class="font-semibold text-gray-900">{{ $certificate->code }}</strong>
            <span class="text-gray-800">no corresponde a ningún certificado emitido oficialmente por la</span>
            <span class="font-semibold text-blue-700">Asociación de Estudiantes de Ingeniería en Sistemas Informáticos
                (ASEIS)</span>
            ni por la
            <span class="font-semibold text-blue-700">Facultad Multidisciplinaria Oriental (FMO)</span>
            de la
            <span class="font-semibold text-blue-700">Universidad de El Salvador (UES)</span>.
        </p>

        <p class="mt-4 max-w-2xl mx-auto text-gray-600 text-base italic">
            Por favor, revisa que el código ingresado sea correcto o comunícate con los organizadores del evento
            para obtener asistencia adicional.
        </p>
    </div>
    @endif
</main>

<script>
    async function ensureFontsLoaded() {
        if (document.fonts && document.fonts.ready) {
            try { await document.fonts.ready; } catch (e) { /* ignore */ }
        }
    }

    function autoFitRecipientName(element, maxFontSize = 56, minFontSize = 20) {
        if (!element || !element.isConnected) return;

        // guardamos estado previo
        const prevWhite = element.style.whiteSpace;
        const prevDisplay = element.style.display;

        // Forzamos single-line para medir correctamente
        element.style.whiteSpace = 'nowrap';
        element.style.display = 'inline-block'; // evita problemas de contenedor flexible

        // Calculamos ancho disponible real: ancho del contenedor padre (diploma-content)
        // menos paddings del elemento si existen.
        const parent = element.parentElement || element;
        const parentRect = parent.getBoundingClientRect();
        const parentStyle = getComputedStyle(parent);

        // ancho interior del padre (content-box)
        const parentPaddingLeft = parseFloat(parentStyle.paddingLeft || 0);
        const parentPaddingRight = parseFloat(parentStyle.paddingRight || 0);
        const availableWidth = Math.max(10, parentRect.width - parentPaddingLeft - parentPaddingRight);

        // Búsqueda binaria entre min y max
        let low = minFontSize;
        let high = maxFontSize;
        let best = minFontSize;

        // Aseguramos que al menos mid se aplique una vez
        while (low <= high) {
            const mid = Math.floor((low + high) / 2);
            element.style.fontSize = mid + 'px';

            // Forzar reflow y medir
            const textWidth = element.scrollWidth;

            if (textWidth <= availableWidth) {
                best = mid;        // mid cabe, intentar más grande
                low = mid + 1;
            } else {
                high = mid - 1;    // mid no cabe, reducir
            }
        }

        element.style.fontSize = best + 'px';

        // Fallback extra (si por redondeo sigue desbordando)
        let safety = 0;
        while (element.scrollWidth > availableWidth && parseFloat(element.style.fontSize) > minFontSize && safety < 20) {
            const cur = Math.max(minFontSize, Math.floor(parseFloat(element.style.fontSize) - 1));
            element.style.fontSize = cur + 'px';
            safety++;
        }

        // restaurar propiedades opcionales
        element.style.whiteSpace = prevWhite;
        element.style.display = prevDisplay;
    }

    async function generateDiplomas(dataArray, namefile = null) {
        const pdf = new jsPDF({ orientation: "landscape", unit: "px", format: [1000, 707] });

        await ensureFontsLoaded();

        const capitalizeName = (name) =>
            name
                .toLocaleLowerCase('es-ES')
                .replace(/\p{L}+(-\p{L}+)?/gu, (word) =>
                    word.charAt(0).toLocaleUpperCase('es-ES') + word.slice(1)
                );

        for (let i = 0; i < dataArray.length; i++) {
            const { recipient_name, event_name, date, qr_code, code } = dataArray[i];

            const template = document.querySelector(".diploma-container").cloneNode(true);
            template.querySelector(".recipient-name").textContent = capitalizeName(recipient_name);
            template.querySelector(".event_name").textContent = event_name;
            template.querySelector(".date").textContent = date;
            template.querySelector(".qr-code img").src = qr_code;
            template.querySelector(".unique_code .code").textContent = code;
            template.querySelector(".diploma-container .logo-text").style.marginBottom = "20px";
            template.querySelector(".diploma-container .seal-inner span").style.marginBottom = "10px";

            Object.assign(template.style, {
                position: "fixed",
                top: "-2000px",
                left: "0",
                display: "block"
            });

            document.body.appendChild(template);

            await new Promise(requestAnimationFrame);
            autoFitRecipientName(template.querySelector(".recipient-name"), 56, 10);
            await new Promise(requestAnimationFrame);

            const canvas = await html2canvas(template, { scale: 1.5 });
            const imgData = canvas.toDataURL("image/jpeg", 0.8);

            pdf.addImage(imgData, "JPEG", 0, 0, 1000, 707);

            document.body.removeChild(template);
        }

        const eventSlug = dataArray[0].event_name.toLowerCase().replaceAll("-", " ").replace(/[^\w\s]/gi, '').replace(/\s+/g, '_');
        const dateNow = new Date();
        const formattedDate = `${dateNow.getFullYear()}-${dateNow.getMonth() + 1}-${("0" + dateNow.getDate()).slice(-2)}`;
        pdf.save(namefile || `diplomas_${eventSlug}_numitems_${dataArray.length}_date_${formattedDate}.pdf`);
    }

    document.addEventListener("DOMContentLoaded", () => {
        let recipientName = document.querySelector('.recipient-name');
        if (recipientName != null) {
            autoFitRecipientName(recipientName, 56, 10);
        }
    })

    async function descargar() {
        let name_certificado = @this.certificate.recipient_name;
        let eventoSlug = @this.certificate.event_name.toLowerCase().replaceAll("-", " ").replace(/[^\w\s]/gi, '').replace(/\s+/g, '_');
        const btn = document.querySelector('.btn-diploma');
        let originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin" style="margin-right: 5px;"></i> Generando PDF...`;
        try {
            let data = {...@this.certificate};
            let dataArray = [data];
            const dateNow = new Date();
            const formattedDate = `${dateNow.getFullYear()}-${dateNow.getMonth() + 1}-${("0" + dateNow.getDate()).slice(-2)}`;
            await generateDiplomas(dataArray, `diploma_${eventoSlug}_${name_certificado.toLowerCase().replaceAll("-", " ").replace(/\s+/g, '-')}_date_${formattedDate}.pdf`);
            await Livewire.dispatch('pdfResult', {
                data : {
                    success: true,
                    message: 'PDF generado correctamente.'
                }
            });
            Alert(
                '¡Éxito!',
                'El diploma se ha generado correctamente.',
                'success'
            );
        } catch (error) {
            console.error('Error generating diploma:', error);
            await Livewire.dispatch('pdfResult', {
                data : {
                    success: false,
                    message: 'Error al generar el PDF: '+error.message
                }
            });
            Alert(
                'Error',
                'Ocurrió un error al generar el diploma. Por favor, inténtalo de nuevo.',
                'error'
            );
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
</script>
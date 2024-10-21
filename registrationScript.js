document.addEventListener('DOMContentLoaded', function() {
    const feedbackMessage = document.getElementById('feedback-message');

    function showMessage(message, type = 'info') {
        feedbackMessage.textContent = message;
        feedbackMessage.className = type; // Assegna una classe per il tipo di messaggio (info, success, error)
    }

    function fetchExams() {
        fetch('get_exams.php')
            .then(response => response.json())
            .then(data => {
                const examList = document.getElementById('exam-list');
                examList.innerHTML = ''; // Pulisce la lista degli esami

                data.forEach(exam => {
                    const examElement = document.createElement('div');
                    examElement.className = 'exam-item';

                    const examTime = new Date(exam.orario).toLocaleString();
                    const isOpen = exam.is_open ? '' : ' (Non disponibile)';

                    examElement.innerHTML = `
                        <div class="exam-info">
                            <span class="exam-name">${exam.nome}</span>
                            <span class="exam-time">Orario: ${examTime}</span>
                        </div>
                        <div class="exam-availability">
                            <span>Posti</span>
                            <span class="availability-number">${exam.posti_disponibili}</span>
                        </div>
                        <button ${!exam.is_open ? 'disabled' : ''} data-id="${exam.id}">
                            ${exam.is_open ? 'Registrati' : 'Registrazione non disponibile'}
                        </button>
                    `;

                    examList.appendChild(examElement);
                });

                // Aggiungi l'evento di clic sui pulsanti
                document.querySelectorAll('button').forEach(button => {
                    button.addEventListener('click', function() {
                        const examId = this.getAttribute('data-id');

                        fetch('registerExam.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams({ exam_id: examId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            showMessage(data.message, data.status);
                            if (data.status === 'success') {
                                this.disabled = true;
                                this.innerText = 'Registrato';
                            }
                        });
                    });
                });
            });
    }

    // Chiama fetchExams subito dopo il caricamento della pagina
    fetchExams();

    // Esegui fetchExams ogni 10 secondi
    setInterval(fetchExams, 10000);
});

document.addEventListener('DOMContentLoaded', function() {
    const currentDateElement = document.getElementById('current-date');
    const eventList = document.getElementById('event-list');
    const calendarElement = document.getElementById('calendar');
    const currentYearElement = document.getElementById('current-year');
    const currentMonthElement = document.getElementById('current-month');

    let currentDate = new Date();

    function updateCurrentDate(date) {
        const options = { weekday: 'short', day: 'numeric', month: 'long' };
        currentDateElement.textContent = date.toLocaleDateString('it-IT', options).toUpperCase();
    }

    function fetchEvents(date) {
        const formattedDate = date.toISOString().split('T')[0];
        fetch(`fetch_events.php?date=${formattedDate}`)
            .then(response => response.json())
            .then(events => {
                const dateCells = calendarElement.querySelectorAll('td[data-date]');
                dateCells.forEach(cell => {
                    const cellDate = cell.dataset.date;
                    const hasEvents = events.some(event => event.date === cellDate);
                    let indicator = cell.querySelector('.event-indicator');

                    if (hasEvents) {
                        if (!indicator) {
                            indicator = document.createElement('div');
                            indicator.className = 'event-indicator';
                            cell.appendChild(indicator);
                        }
                    } else {
                        if (indicator) {
                            cell.removeChild(indicator);
                        }
                    }
                });
            })
            .catch(error => console.error('Errore:', error));
    }

    function createCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        currentYearElement.textContent = year;
        currentMonthElement.textContent = new Date(year, month, 1).toLocaleString('it-IT', { month: 'long' });

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);

        let calendarHTML = '<table>';
        calendarHTML += '<tr><th>Lun</th><th>Mar</th><th>Mer</th><th>Gio</th><th>Ven</th><th>Sab</th><th>Dom</th></tr>';

        let day = 1;
        let startDay = (firstDay.getDay() + 6) % 7; // Regola l'inizio della settimana a Lunedì

        for (let i = 0; i < 6; i++) {
            calendarHTML += '<tr>';
            for (let j = 0; j < 7; j++) {
                if (i === 0 && j < startDay) {
                    calendarHTML += '<td></td>';
                } else if (day > lastDay.getDate()) {
                    calendarHTML += '<td></td>';
                } else {
                    calendarHTML += `<td data-date="${year}-${(month + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}">${day}</td>`;
                    day++;
                }
            }
            calendarHTML += '</tr>';
            if (day > lastDay.getDate()) break;
        }

        calendarHTML += '</table>';
        calendarElement.innerHTML = calendarHTML;

        const dateCells = calendarElement.querySelectorAll('td[data-date]');
        dateCells.forEach(cell => {
            cell.addEventListener('click', function() {
                const selectedDate = new Date(this.dataset.date);
                updateCurrentDate(selectedDate);
                fetchEvents(selectedDate);
            });
        });

        // Fetch events for the current month
        fetchEvents(date);
    }

    document.getElementById('prev-year').addEventListener('click', () => {
        currentDate.setFullYear(currentDate.getFullYear() - 1);
        createCalendar(currentDate);
    });

    document.getElementById('next-year').addEventListener('click', () => {
        currentDate.setFullYear(currentDate.getFullYear() + 1);
        createCalendar(currentDate);
    });

    document.getElementById('prev-month').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        createCalendar(currentDate);
    });

    document.getElementById('next-month').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        createCalendar(currentDate);
    });

    updateCurrentDate(currentDate);
    createCalendar(currentDate);
});

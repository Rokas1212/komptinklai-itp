document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('mechanicModal');
    const dateInput = document.getElementById('selected-date');
    const timesTableBody = document.getElementById('available-times-table').querySelector('tbody');
    const selectedTimeInput = document.getElementById('selected-time');
    const carSelect = document.getElementById('selected-car');

    let selectedMechanicId = null;

    // When a card is clicked, set mechanic ID and open the modal
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', () => {
            selectedMechanicId = card.id.split('-')[2];
            document.getElementById('modal-meistro-id').value = selectedMechanicId;

            // Clear previous selections
            dateInput.value = '';
            timesTableBody.innerHTML = '';
            selectedTimeInput.value = '';
            carSelect.value = '';

            // Show the modal
            $(modal).modal('show');
        });
    });

    // Ensure car selection is checked on form submit
    const form = document.getElementById('time-selection-form');
    form.addEventListener('submit', (e) => {
        const selectedDate = dateInput.value;
        const selectedTime = selectedTimeInput.value;

        if (!carSelect.value) {
            e.preventDefault();
            alert('Prašome pasirinkti automobilį.');
            return;
        }

        // Validate if the selected date and time are in the past
        const now = new Date();
        const selectedDateTime = new Date(`${selectedDate}T${selectedTime}`);

        if (selectedDateTime < now) {
            e.preventDefault();
            alert('Rezervacijos laikas negali būti praeityje.');
            return;
        }
    });

    // Fetch available times when a date is selected
    dateInput.addEventListener('change', async () => {
        if (!selectedMechanicId || !dateInput.value) return;

        try {
            const response = await fetch(`helpers/fetchAvailableTimes.php?meistro_id=${selectedMechanicId}&date=${dateInput.value}`);
            const data = await response.json();

            if (data.success) {
                timesTableBody.innerHTML = ''; // Clear previous rows

                data.times.forEach(time => {
                    const row = document.createElement('tr');
                    const timeCell = document.createElement('td');
                    const statusCell = document.createElement('td');

                    timeCell.textContent = time;
                    if (data.unavailable.includes(time)) {
                        statusCell.innerHTML = '<span class="text-danger">Užimtas</span>';
                    } else {
                        statusCell.innerHTML = `<button type="button" class="btn btn-sm btn-success select-time" data-time="${time}">Pasirinkti</button>`;
                    }

                    row.appendChild(timeCell);
                    row.appendChild(statusCell);
                    timesTableBody.appendChild(row);
                });

                // Add event listener for "Pasirinkti" buttons
                document.querySelectorAll('.select-time').forEach(button => {
                    button.addEventListener('click', (e) => {
                        const selectedTime = e.target.dataset.time;
                        selectedTimeInput.value = selectedTime; // Set the selected time in the hidden input
                        alert(`Pasirinkote laiką: ${selectedTime}`);
                    });
                });
            } else {
                timesTableBody.innerHTML = '<tr><td colspan="2" class="text-center">Nėra galimų laikų.</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching available times:', error);
            alert('Įvyko klaida gaunant laikus.');
        }
    });
});

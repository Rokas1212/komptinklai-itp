async function deleteUnavailableTime(prieinamumoId) {
    if (!confirm("Ar tikrai norite pašalinti šį užimtumo laiką?")) return;

    try {
        const response = await fetch('deleteUnavailability.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: prieinamumoId }),
        });

        const data = await response.json();
        if (data.success) {
            document.getElementById(`row-${prieinamumoId}`).remove();
            alert("Užimtumo laikas sėkmingai pašalintas.");
        } else {
            alert("Klaida pašalinant užimtumo laiką: " + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert("Įvyko klaida. Pabandykite dar kartą.");
    }
}


function setMechanicId(meistroId) {
    document.getElementById('meistro_id').value = meistroId;
}
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById('progressForm');
    const modal = document.getElementById('progressModal');

    // 1. Validazione input
    form.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', e => {
            if (e.target.value < 0) {
                e.target.value = 0;
                alert("Valori negativi non consentiti!");
            }
        });
    });

    // 2. Gestione salvataggio
    document.getElementById("saveProgress").addEventListener("click", async () => {
        try {
            // Aggiungi validazione lato client
            const formData = new FormData(form);
            const pagineOggi = parseInt(formData.get('pagine-lette-oggi')) || 0;
            const pagineLibro = parseInt(formData.get('pagine-libro-corrente')) || 0;

            if (pagineOggi < 0 || pagineLibro < 0) {
                throw new Error("I valori non possono essere negativi");
            }
        } catch (error) {
            console.error('Errore di validazione:', error);
            alert(error.message);
            return;
        }
        try {
            // Verifica preliminare obiettivi
            const check = await fetch('../public/checkObiettivi.php');
            if (!check.ok) throw new Error(`HTTP ${check.status}`);
            const { validi, message } = await check.json();
            if (!validi) return alert(message);

            // Invio dati
            const formData = new FormData(form);
            const response = await fetch('../public/updateStatistiche.php', {
                method: 'POST',
                body: formData
            });

            // Gestione risposta
            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch {
                throw new Error(`Risposta non valida: ${text.slice(0, 50)}`);
            }

            if (!response.ok || data.status !== 'success') {
                throw new Error(data.message || 'Errore nel salvataggio');
            }

            // Aggiornamento UI
                        await Promise.all([
                updateAllProgressBars(),
                updateGlobalUserData()
            ]);

        } catch (error) {
            console.error('Salvataggio fallito:', error);
            alert(error.message);
        }
     });
    });

     // 3. Funzioni di supporto modificate
     async function updateAllProgressBars() {
        try {
            const types = ['pagineQuotidiane', 'libriAnnuali', 'pagineLibroCorrente'];
            for (const type of types) {
                const res = await fetch(`../public/getProgress.php?type=${type}`);
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();
                
                // Aggiungi gestione divisione per zero
                const percent = data.goal > 0 
                    ? Math.min(Math.round((data.current / data.goal) * 100), 100)
                    : 0;
                
                updateProgressBar(type, percent);
            }
        } catch (error) {
            console.error('Update error:', error);
            alert("Errore nell'aggiornamento delle statistiche");
        }
    }

    // funzione per aggiornare i dati globali
    async function updateGlobalUserData() {
        try {
            const response = await fetch('../public/getUserData.php');
            const data = await response.json();
            Object.assign(userData, data);
        } catch (error) {
            console.error('User data update failed:', error);
        }
    }

    function updateProgressBar(type, percent) {
        const bar = document.querySelector(`[data-goal-type="${type}"]`);
        if (!bar) return;

        bar.dataset.value = percent;
        bar.querySelector('.h2').textContent = `${percent}%`;
        
        const left = bar.querySelector('.progress-left .progress-bar');
        const right = bar.querySelector('.progress-right .progress-bar');
        const deg = percent * 3.6;
        
        right.style.transform = `rotate(${Math.min(deg, 180)}deg)`;
        left.style.transform = `rotate(${Math.max(deg - 180, 0)}deg)`;
    }

    // 4. Gestione UI modale
    document.getElementById('searchBookBtn').addEventListener('click', () => {
        form.querySelectorAll('.field-toggle').forEach(el => el.style.display = 'none');
        document.getElementById('isbnGroup').style.display = 'block';
    });

    document.getElementById('addManuallyBtn').addEventListener('click', () => {
        form.querySelectorAll('.field-toggle').forEach(el => el.style.display = 'block');
    });
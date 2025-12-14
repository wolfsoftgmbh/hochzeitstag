document.addEventListener('DOMContentLoaded', () => {
    
    // Globale Config nutzen oder Fallback
    const CONFIG = (typeof HOCHZEITSTAG_CONFIG !== 'undefined') ? HOCHZEITSTAG_CONFIG : {
        weddingDate: "2025-09-06T11:02:00",
        firstContactDate: "2014-01-11T19:02:00",
        firstMeetDate: "2014-04-01T21:02:00",
        backgroundImage: "",
        quotes: ["Liebe ist alles."]
    };

    // Background Image Logic (falls gewünscht, sonst CSS Gradient lassen)
    if (CONFIG.backgroundImage) {
        // Optional: Wenn du das Bild als Hintergrund für die ganze Seite willst
        // document.body.style.backgroundImage = `url('${CONFIG.backgroundImage}')`;
        // document.body.style.backgroundSize = "cover";
        // document.body.style.backgroundPosition = "center";
    }

    const WEDDING_DATE = new Date(CONFIG.weddingDate);
    
    const historyEvents = [
        { name: "Erster Kontakt", date: CONFIG.firstContactDate },
        { name: "Zusammen", date: CONFIG.firstMeetDate },
        { name: "Hochzeit", date: CONFIG.weddingDate }
    ];

    // DOM Elements
    const elYears = document.getElementById('val-years');
    const elDays = document.getElementById('val-days');
    const elHours = document.getElementById('val-hours');
    const elMinutes = document.getElementById('val-minutes');
    const elTotalSeconds = document.getElementById('total-seconds');
    const elTotalDays = document.getElementById('total-days');
    const elQuoteDisplay = document.getElementById('quote-display');
    const elWeddingDateDisplay = document.getElementById('wedding-date-display');
    
    // Neue Container IDs (Divs statt Table)
    const elMilestoneList = document.getElementById('milestone-list');
    const elHistoryList = document.getElementById('history-list');
    const elNextAnniversary = document.getElementById('next-anniversary');

    /* --- Hilfsfunktionen --- */

    function displayRandomQuote() {
        if (!elQuoteDisplay || !CONFIG.quotes) return;
        const randomIndex = Math.floor(Math.random() * CONFIG.quotes.length);
        elQuoteDisplay.innerHTML = `„${CONFIG.quotes[randomIndex]}“`;
    }

    function formatShortDate(date) {
        return date.toLocaleDateString('de-DE', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
    }

    // Zeig nur Tage an
    function getRemainingDays(targetDate) {
        const now = new Date();
        const diff = targetDate - now;
        if (diff <= 0) return null; 
        return Math.ceil(diff / (1000 * 60 * 60 * 24));
    }

    function calculateTimeComponents(startDate, endDate) {
        let diff = endDate - startDate;
        if (diff < 0) diff = 0;

        const totalSeconds = Math.floor(diff / 1000);
        const totalDays = Math.floor(totalSeconds / (3600 * 24));
        
        // Exakte Jahre/Tage/Stunden Berechnung
        let tempDate = new Date(startDate);
        let years = 0;
        while(true) {
            tempDate.setFullYear(tempDate.getFullYear() + 1);
            if(tempDate > endDate) {
                tempDate.setFullYear(tempDate.getFullYear() - 1);
                break;
            }
            years++;
        }
        const diffRest = endDate - tempDate;
        const days = Math.floor(diffRest / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diffRest % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diffRest % (1000 * 60 * 60)) / (1000 * 60));

        return { years, days, hours, minutes, totalSeconds, totalDays };
    }

    /* --- Haupt-Update Funktion --- */
    function updateTimer() {
        const now = new Date();
        const time = calculateTimeComponents(WEDDING_DATE, now);

        // Header Counters
        if (elYears) elYears.innerText = time.years;
        if (elDays) elDays.innerText = time.days;
        if (elHours) elHours.innerText = time.hours;
        if (elMinutes) elMinutes.innerText = time.minutes;
        
        // Footer Stats
        if (elTotalSeconds) elTotalSeconds.innerText = time.totalSeconds.toLocaleString('de-DE');
        if (elTotalDays) elTotalDays.innerText = time.totalDays.toLocaleString('de-DE');

        // Startdatum Info
        if (elWeddingDateDisplay) {
            elWeddingDateDisplay.innerText = `Unsere Reise begann am ${formatShortDate(WEDDING_DATE)}`;
        }

        // --- Render HISTORY Timeline ---
        if (elHistoryList) {
            let html = '';
            historyEvents.forEach(evt => {
                const d = new Date(evt.date);
                const t = calculateTimeComponents(d, now);
                // Erstelle HTML für Timeline Item
                html += `
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="t-label">${evt.name}</span>
                        <span class="t-date">${formatShortDate(d)}</span>
                        <span class="t-countdown">vor ${t.years} Jahren, ${t.days} Tagen</span>
                    </div>
                </div>`;
            });
            elHistoryList.innerHTML = html;
        }

        // --- Render MILESTONES Timeline ---
        if (elMilestoneList) {
            // Logik zur Meilenstein-Berechnung (vereinfacht für Übersicht)
            // Hier generieren wir dynamisch die nächsten 3 Events (100er Schritte oder Jahrestage)
            
            const milestones = [];
            const oneDayMs = 86400000;
            
            // 1. Nächster Jahrestag
            let nextAnniv = new Date(WEDDING_DATE);
            nextAnniv.setFullYear(now.getFullYear());
            if (nextAnniv < now) nextAnniv.setFullYear(now.getFullYear() + 1);
            
            let yearNum = nextAnniv.getFullYear() - WEDDING_DATE.getFullYear();
            milestones.push({ date: nextAnniv, label: `${yearNum}. Hochzeitstag` });

            // 2. Nächster 1000er oder 100er Tag
            // Berechne Tage seit Hochzeit
            const daysPassed = Math.floor((now - WEDDING_DATE) / oneDayMs);
            const next1000 = (Math.floor(daysPassed / 1000) + 1) * 1000;
            const date1000 = new Date(WEDDING_DATE.getTime() + (next1000 * oneDayMs));
            milestones.push({ date: date1000, label: `${next1000}. Tag` });

            // 3. Geburtstage (wenn konfiguriert)
            if (CONFIG.birthdays) {
                for (const [name, dateStr] of Object.entries(CONFIG.birthdays)) {
                    if (dateStr) {
                        const bdayDate = new Date(dateStr);
                        let nextBday = new Date(bdayDate);
                        nextBday.setFullYear(now.getFullYear());
                        
                        // Wenn der Geburtstag dieses Jahr schon war, dann nächstes Jahr
                        if (nextBday < now) {
                            nextBday.setFullYear(now.getFullYear() + 1);
                        }
                        
                        // Name formatieren (erster Buchstabe groß)
                        const formattedName = name.charAt(0).toUpperCase() + name.slice(1);
                        milestones.push({ date: nextBday, label: `Geburtstag ${formattedName}` });
                    }
                }
            }

            // Sortieren
            milestones.sort((a,b) => a.date - b.date);

            // HTML Generieren
            let mHtml = '';
            milestones.forEach(m => {
                const daysLeft = getRemainingDays(m.date);
                mHtml += `
                <div class="timeline-item">
                    <div class="timeline-dot" style="background: #fff; border-color: var(--primary-color);"></div>
                    <div class="timeline-content">
                        <span class="t-label">${m.label}</span>
                        <span class="t-date">${formatShortDate(m.date)}</span>
                        <span class="t-countdown">in ${daysLeft} Tagen</span>
                    </div>
                </div>`;
            });
            elMilestoneList.innerHTML = mHtml;

            // Update Footer Pill (Nächster Hochzeitstag)
            if (elNextAnniversary && milestones.length > 0) {
                // Wir nehmen an, der erste Milestone ist relevant oder wir suchen spezifisch den Jahrestag
                const anniv = milestones.find(m => m.label.includes("Hochzeitstag"));
                if (anniv) {
                    const dLeft = getRemainingDays(anniv.date);
                    elNextAnniversary.innerText = `Noch ${dLeft} Tage bis zum ${anniv.label}`;
                }
            }
        }
    }

    displayRandomQuote();
    updateTimer();
    setInterval(updateTimer, 1000);
});